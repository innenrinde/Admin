<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends CrudController
{
    /**
     * @var array
     */
    private array $columns = [
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
            'title' => 'Password',
            'type' => 'string',
            'field' => 'password',
            'width' => 200,
            'hidden' => true
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
        [
            'title' => 'Is Admin',
            'type' => 'boolean',
            'field' => 'isAdmin',
        ],
    ];

    public function __construct(private readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $userPasswordHasher) {
    }

    #[Route('/users', name: 'app_users')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'columns' => $this->columns,
        ]);
    }

    #[Route('/users/list', name: 'app_users_list')]
    public function getRows(Request $request): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $data = array_map(function ($user) {
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
        }, $users);

        return $this->httpResponse($data);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/users/add', name: 'app_users_add')]
    public function addRow(Request $request): Response
    {
        return $this->render('user/add.html.twig', [
            'columns' => $this->columns,
        ]);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/users/edit', name: 'app_users_edit')]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if ($this->isCreate($request)) {
            $data['id'] = 0;
        } else if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        if (!isset($data['email'])) {
            return $this->httpResponse($data['id'], false, "User email missing");
        }

        if ($this->isCreate($request) && !isset($data['password'])) {
            return $this->httpResponse($data['id'], false, "User password missing");
        }

        $this->em->getFilters()->disable('removedRow');
        $userCheck = $this->em->getRepository(User::class)->findOneBy(["email" => $data['email']]);
        if ($userCheck && $userCheck->getId() != $data['id']) {
            return $this->httpResponse($data['id'], false, "User email already exists");
        }

        /**
         * @var User $user
         */
        $user = new User();
        if ($this->isEdit($request)) {
            $user = $this->em->getRepository(User::class)->find($data['id']);
        }

        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setVerified($data['isVerified'] ?? false);
        $user->setIsActive($data['isActive'] ?? false);
        $user->setLastLogged(new \DateTime($data['lastLogged']));
        $roles = ($data['isAdmin'] ?? false) ? [User::ROLE_ADMIN] : [User::ROLE_USER];
        $user->setRoles($roles);

        $plainPassword = $data['password'] ?? null;
        if ($plainPassword) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
        }

        $this->em->persist($user);
        $this->em->flush();

        $message = $this->isEdit($request) ? "User successfully edited" : "User successfully created";

        return $this->httpResponse($data['id'], true, $message, $data);
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

        return $this->httpResponse($userId, true, "User successfully deleted");
    }
}
