<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends CrudController
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
            'width' => 200,
        ],
        [
            'title' => 'Title',
            'type' => 'string',
            'field' => 'title',
        ],
    ];

    public function __construct(private readonly EntityManagerInterface $em, private HttpService $httpService) {
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'columns' => $this->columns,
        ]);
    }

    #[Route('/categories/list', name: 'app_categories_list')]
    public function getRows(Request $request): JsonResponse
    {
        $rows = $this->em->getRepository(Category::class)->findAll();

        $data = array_map(function (Category $row) {
            return [
                'id' => $row->getId(),
                'title' => $row->getTitle(),
            ];
        }, $rows);

        return $this->httpService->response($data);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/categories/add', name: 'app_categories_add')]
    public function addRow(Request $request): Response
    {
        return $this->render('category/add.html.twig', [
            'columns' => $this->columns,
        ]);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/categories/edit', name: 'app_categories_edit')]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if ($this->isCreate($request)) {
            $data['id'] = 0;
        } else if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        $entity = new Category();
        if ($this->isEdit($request)) {
            $entity = $this->em->getRepository(Category::class)->find($data['id']);
        }

        if (!isset($data['title'])) {
            return $this->httpService->response($data['id'], false, "Category title missing");
        }

        $entity->setTitle($data['title']);

        $this->em->persist($entity);
        $this->em->flush();

        $message = $this->isEdit($request) ? "Category successfully edited" : "Category successfully created";

        return $this->httpService->response($data['id'], true, $message, $data);
    }

    /**
     * Delete an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/categories/delete', name: 'app_categories_delete')]
    public function deleteRow(Request $request): JsonResponse
    {
        $id = (int)$request->getContent(false);

        /**
         * @var Category $entity
         */
        $entity = $this->em->getRepository(Category::class)->find($id);

        $entity->setRemoved(true);
        $this->em->persist($entity);
        $this->em->flush();

        return $this->httpService->response($id, true, "Category successfully deleted");
    }
}
