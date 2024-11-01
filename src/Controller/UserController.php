<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
    public function getUsers(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
     */
    #[Route('/users/edit', name: 'app_users_edit')]
    public function usersAdd(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->toArray();

        if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        /**
         * @var User $user
         */
        $userCheck = $this->em->getRepository(User::class)->findOneBy(["email" => $data['email']]);
        if ($userCheck && $userCheck->getId() != $data['id']) {
            return new JsonResponse([
                'id' => $data['id'],
                'ok' => false,
                'message' => "User email already exists",
            ],
                Response::HTTP_OK);
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

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Delete an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/users/delete', name: 'app_users_delete')]
    public function usersDelete(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userId = (int)$request->getContent(false);

        /**
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->find($userId);

        $responseOk = true;
        if ($user->isAdmin()) {
            $responseOk = false;
        } else {
            $user->setRemoved(true);
            $this->em->persist($user);
            $this->em->flush();
        }

        return new JsonResponse([
            'id' => $userId,
            'ok' => $responseOk,
            'message' => $responseOk ? null : "Can't delete an admin user",
        ],
            Response::HTTP_OK);
    }
}
