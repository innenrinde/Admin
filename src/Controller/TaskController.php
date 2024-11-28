<?php

namespace App\Controller;

use App\Entity\Task;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends CrudController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpService $httpService
    ) {
    }

    #[Route('/tasks', name: 'app_tasks')]
    public function index(): Response
    {
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
    public function getRows(Request $request): JsonResponse
    {
        $tasks = $this->em->getRepository(Task::class)->findAll();

        $data = array_map(function ($task) {
            return [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'createdDate' => $this->dateFormat($task->getCreatedDate()),
                'editedDate' => $this->dateFormat($task->getModifiedDate()),
            ];
        }, $tasks);

        return $this->httpService->response($data);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/tasks/add', name: 'app_tasks_add')]
    public function addRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/tasks/edit', name: 'app_tasks_edit')]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Delete an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/tasks/delete', name: 'app_tasks_delete')]
    public function deleteRow(Request $request): JsonResponse
    {
        $userId = (int)$request->getContent(false);

        return new JsonResponse([
            'id' => $userId
        ],
            Response::HTTP_OK);
    }
}
