<?php

namespace App\Controller;

use App\Api\Constraints\NullForCreation;
use App\Api\TableBuilder;
use App\Entity\Category;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Api\Constraints\NotBlank;

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
            'constraints' => [
                NullForCreation::class => 'No id found',
            ]
        ],
        [
            'title' => 'Title',
            'type' => TextType::class,
            'field' => 'title',
            'width' => 'w100',
            'constraints' => [
                NotBlank::class => 'Please enter a category title',
            ]
        ],
    ];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpService $httpService,
        private readonly TableBuilder $tableBuilder
    ) {
        parent::__construct($this->em);
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', []);
    }

    #[Route('/categories/list', name: 'app_categories_list', methods: ['GET'])]
    public function getRows(Request $request): JsonResponse
    {

        $columns = $this->tableBuilder->getColumns($this->columns);

        $data = $this->filteredRows(Category::class, $request->query->all());

        $rows = array_map(function (Category $row) {
            return [
                'id' => $row->getId(),
                'title' => $row->getTitle(),
            ];
        }, $data['rows']);

        return $this->httpService->response($rows, $columns, $data['pager']);
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
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    /**
     * Create a category
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/categories/create', name: 'app_categories_create', methods: ['PUT'])]
    public function createRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        try {
            $entity = $this->tableBuilder->create(Category::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($entity->getId(), true, "Category successfully created", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
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

        try {
            $entity = $this->tableBuilder->save(Category::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($data['id'], true, "Category successfully edited", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
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

        $entity = $this->em->getRepository(Category::class)->find($id);
        $entity->setRemoved(true);
        $this->em->persist($entity);
        $this->em->flush();

        return $this->httpService->response($id, true, "Category successfully deleted");
    }
}
