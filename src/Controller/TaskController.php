<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/tasks', name: 'app_tasks')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('task/index.html.twig', [
            'columns' => [
                [
                    'title' => 'ID',
                    'type' => 'number',
                    'field' => 'id',
                    'isPk' => true,
                ],
                [
                    'title' => 'Title',
                    'type' => 'string',
                    'field' => 'title',
                ],
                [
                    'title' => 'Description',
                    'type' => 'string',
                    'field' => 'description',
                ],
            ],
        ]);
    }

    #[Route('/tasks/list', name: 'app_tasks_list')]
    public function getTasks(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $tasks = $this->em->getRepository(Task::class)->findAll();

        return new JsonResponse(
            [
                'rows' => array_map(function ($task) {
                    return [
                        'id' => $task->getId(),
                        'title' => $task->getTitle(),
                        'description' => $task->getDescription(),
                        'createdDate' => $this->dateFormat($task->getCreatedDate()),
                        'editedDate' => $this->dateFormat($task->getModifiedDate()),
                    ];
                }, $tasks)
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
    #[Route('/tasks/edit', name: 'app_tasks_edit')]
    public function tasksAdd(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->toArray();

        if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        /**
         * @var Task $user
         */
//        $user = $this->em->getRepository(User::class)->find($data['id']);
//        $user->setName($data['name']);
//        $user->setSurname($data['surname']);
//        $user->setVerified($data['isVerified']);
//        $this->em->persist($user);
//        $this->em->flush();

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Delete an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/tasks/delete', name: 'app_tasks_delete')]
    public function tasksDelete(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userId = (int)$request->getContent(false);

        /**
         * @var Task $user
         */
//        $user = $this->em->getRepository(User::class)->find($userId);
//        $this->em->remove($user);
//        $this->em->flush();

        return new JsonResponse([
            'id' => $userId
        ],
            Response::HTTP_OK);
    }
}
