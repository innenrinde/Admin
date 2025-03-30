<?php

namespace App\Controller;

use App\Builder\Constraints\EmailFormat;
use App\Builder\Constraints\NullForCreation;
use App\Builder\TableBuilder;
use App\Entity\User;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends CrudController
{
    /**
     * @var array
     */
    private array $columns = [
        [
            'title' => 'ID',
            'type' => NumberType::class,
            'field' => 'id',
            'isPk' => true,
            'constraints' => [
                NullForCreation::class => 'No id found',
            ]
        ],
        [
            'title' => 'Name',
            'type' => TextType::class,
            'field' => 'name',
        ],
        [
            'title' => 'Surname',
            'type' => TextType::class,
            'field' => 'surname',
        ],
        [
            'title' => 'Email',
            'type' => TextType::class,
            'field' => 'email',
            'width' => 200,
            'constraints' => [
                EmailFormat::class => 'Invalid email address',
            ]
        ],
        [
            'title' => 'Password',
            'type' => PasswordType::class,
            'field' => 'password',
            'width' => 200,
            'hidden' => true,
        ],
        [
            'title' => 'Is Active',
            'type' => CheckboxType::class,
            'field' => 'isActive',
        ],
        [
            'title' => 'Is Verified',
            'type' => CheckboxType::class,
            'field' => 'isVerified',
        ],
        [
            'title' => 'Last login date',
            'type' => DateTimeType::class,
            'field' => 'lastLogged',
            'hidden' => true
        ],
        [
            'title' => 'Is Admin',
            'type' => CheckboxType::class,
            'field' => 'isAdmin',
        ],
        [
            'title' => 'ZKP',
            'type' => CheckboxType::class,
            'field' => 'zkp',
            'hidden' => true
        ],
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpService $httpService,
        private readonly TableBuilder $tableBuilder
    ) {
    }

    #[Route('/users', name: 'app_users')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    #[Route('/users/list', name: 'app_users_list')]
    public function getRows(Request $request): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $data = array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getUserIdentifier(),
                'lastLogged' => $this->dateFormat($user->getLastLogged()),
                'registerDate' => $this->dateFormat($user->getRegisterDate()),
                'isVerified' => $user->isVerified(),
                'isActive' => $user->isActive(),
                'isAdmin' => $user->isAdmin(),
                'zkp' => $user->isZkp()
            ];
        }, $users);

        return $this->httpService->response($data);
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
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/user/create', name: 'app_users_create', methods: ['PUT'])]
    public function createRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        try {
            $entity = $this->tableBuilder->create(User::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($entity->getId(), true, "User successfully created", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
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

        try {
            $entity = $this->tableBuilder->save(User::class, $this->columns, $data);

            $this->em->getFilters()->disable('removedRow');
            $userCheck = $this->em->getRepository(User::class)->findOneBy(["email" => $data['email']]);
            if ($userCheck && $userCheck->getId() != $data['id']) {
                return $this->httpService->response($data['id'], false, "User email already exists");
            }

            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($data['id'], true, "User successfully edited", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
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

        $user = $this->em->getRepository(User::class)->find($userId);

        if ($user->isAdmin()) {
            return $this->httpService->response($userId, false, "Can't delete an admin user");
        }

        $user->setRemoved(true);
        $this->em->persist($user);
        $this->em->flush();

        return $this->httpService->response($userId, true, "User successfully deleted");
    }
}
