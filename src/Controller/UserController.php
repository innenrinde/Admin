<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends CrudController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/users', name: 'app_users')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'columns' => [
                [
                    'title' => 'ID',
                    'type' => 'number',
                    'field' => 'id',
                    'isPk' => true,
                ],
                [
                    'title' => 'Name',
                    'type' => 'string',
                    'field' => 'name',
                ],
                [
                    'title' => 'Surname',
                    'type' => 'string',
                    'field' => 'surname',
                ],
                [
                    'title' => 'Email',
                    'type' => 'string',
                    'field' => 'email',
                    'width' => 200,
                ],
                [
                    'title' => 'Is Active',
                    'type' => 'boolean',
                    'field' => 'isActive',
                ],
                [
                    'title' => 'Is Verified',
                    'type' => 'boolean',
                    'field' => 'isVerified',
                ],
                [
                    'title' => 'Last login date',
                    'type' => 'datetime',
                    'field' => 'lastLogged',
                ],
//                [
//                    'title' => 'Register date',
//                    'type' => 'datetime',
//                    'field' => 'registerDate',
//                ],
                [
                    'title' => 'Is Admin',
                    'type' => 'boolean',
                    'field' => 'isAdmin',
                ],
            ],
        ]);
    }

    #[Route('/users/list', name: 'app_users_list')]
    public function getRows(Request $request): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return new JsonResponse(
            [
                /**
                 * @var User $user
                 */
                'rows' => array_map(function ($user) {
                    return [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'surname' => $user->getSurname(),
                        'email' => $user->getUserIdentifier(),
                        'lastLogged' => $this->dateFormat($user->getLastLogged()),
                        'registerDate' => $this->dateFormat($user->getRegisterDate()),
                        'isVerified' => $user->isVerified(),
                        'isActive' => $user->isActive(),
                        'isAdmin' => $user->isAdmin()
                    ];
                }, $users)
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Custom date time format
     * @param \DateTime|null $date
     * @return string
     */
    private function dateFormat(?\DateTime $date): string
    {
        if ($date) {
            return $date->format("Y-m-d H:i:s");
        }

        return "";
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/users/edit', name: 'app_users_edit')]
    public function addRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        /**
         * @var User $user
         */
        $this->em->getFilters()->disable('removedRow');
        $userCheck = $this->em->getRepository(User::class)->findOneBy(["email" => $data['email']]);
        if ($userCheck && $userCheck->getId() != $data['id']) {
            return $this->httpResponse($data['id'], false, "User email already exists");
        }

        $user = $this->em->getRepository(User::class)->find($data['id']);
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setVerified($data['isVerified']);
        $user->setIsActive($data['isActive']);
        $user->setLastLogged(new \DateTime($data['lastLogged']));
        $roles = $data['isAdmin'] ? [User::ROLE_ADMIN] : [User::ROLE_USER];
        $user->setRoles($roles);

        $this->em->persist($user);
        $this->em->flush();

        return $this->httpResponse($data['id'], true, "User successfully edited", $data);
    }

    /**
     * Delete an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/users/delete', name: 'app_users_delete')]
    public function deleteRow(Request $request): JsonResponse
    {
        $userId = (int)$request->getContent(false);

        /**
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->find($userId);

        if ($user->isAdmin()) {
            return $this->httpResponse($userId, false, "Can't delete an admin user");
        }

        $user->setRemoved(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->httpResponse($userId, false, "User successfully deleted");
    }
}
