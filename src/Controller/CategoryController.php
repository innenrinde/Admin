<?php

namespace App\Controller;

use App\Entity\Category;
use App\Services\HttpService;
use App\Services\TableBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryController extends CrudController
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
            'width' => 'w1',

        ],
        [
            'title' => 'Title',
            'type' => TextType::class,
            'field' => 'title',
            'width' => 'w100',
            'constraints' => [
                NotBlank::class => 'Please enter a title',
            ]
        ],
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private HttpService $httpService,
        private TableBuilder $tableBuilder
    ) {
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    #[Route('/categories/list', name: 'app_categories_list', methods: ['GET'])]
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
     * Create a category
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/categories/add', name: 'app_categories_add', methods: ['GET'])]
    public function addRow(Request $request): Response
    {
        return $this->render('category/add.html.twig', [
            'columns' => $this->columns,
        ]);
    }

    /**
     * Edit a category
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/categories/create', name: 'app_categories_create', methods: ['PUT'])]
    public function createRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $entity = $this->tableBuilder->create(Category::class, $this->columns, $data);
        $this->em->persist($entity);
        $this->em->flush();

        return $this->httpService->response($entity->getId(), true, "Category successfully created", $data);
    }

    /**
     * Edit a category
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/categories/edit', name: 'app_categories_edit', methods: ['POST'])]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        $entity = $this->em->getRepository(Category::class)->find($data['id']);

        if (!isset($data['title'])) {
            return $this->httpService->response($data['id'], false, "Category title missing");
        }

        $entity->setTitle($data['title']);

        $this->em->persist($entity);
        $this->em->flush();

        return $this->httpService->response($data['id'], true, "Category successfully edited", $data);
    }

    /**
     * Delete a category
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/categories/delete', name: 'app_categories_delete', methods: ['DELETE'])]
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
