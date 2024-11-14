<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends CrudController
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
    public function editRow(Request $request): JsonResponse
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
