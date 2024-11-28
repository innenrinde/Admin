<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class AccountController extends AbstractController
{
    /**
     * @var array
     */
    private $columns = [
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
            'placeholder' => 'Leave empty if you don\'t want to change password',
        ],
        [
            'title' => 'ZKP',
            'type' => 'boolean',
            'field' => 'zkp',
        ]
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly HttpService $httpService
    ) {
    }

    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $values = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'email' => $user->getEmail(),
            'zkp' => $user->isZkp(),
        ];

        return $this->render('account/index.html.twig', [
            'columns' => $this->columns,
            'values' => $values,
        ]);
    }

    #[Route('/account_edit', name: 'app_account_edit')]
    public function editAccount(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if (!isset($data['email']) || !$data['email']) {
            return $this->httpService->response($data['id'], false, "User email missing");
        }

        $this->em->getFilters()->disable('removedRow');
        $userCheck = $this->em->getRepository(User::class)->findOneBy(["email" => $data['email']]);
        if ($userCheck && $userCheck->getId() != $data['id']) {
            return $this->httpService->response($data['id'], false, "User email already exists");
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $plainPassword = $data['password'] ?? null;
        if ($plainPassword) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
        }

        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setEmail($data['email']);
        $user->setZkp($data['zkp'] ?? false);

        $this->em->persist($user);
        $this->em->flush();

        return $this->httpService->response($data['id'], true, 'Data successfully saved', []);

    }

}
