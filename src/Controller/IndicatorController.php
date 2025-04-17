<?php

namespace App\Controller;

use App\Builder\Constraints\IpFormat;
use App\Builder\Constraints\NotBlank;
use App\Builder\Constraints\NullForCreation;
use App\Builder\TableBuilder;
use App\Entity\Category;
use App\Entity\Indicator;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndicatorController extends CrudController
{
    /**
     * @var array List columns
     */
    private array $columns = [];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpService $httpService,
        private readonly TableBuilder $tableBuilder
    ) {
        $this->columns = [
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
                'title' => 'Category',
                'type' => ChoiceType::class,
                'entity' => Category::class,
                'field' => 'category',
                'options' => $this->getCategoriesList(),
                'constraints' => [
                    NotBlank::class => 'Please select a category',
                ]
            ],
            [
                'title' => 'Title',
                'type' => TextType::class,
                'field' => 'title',
                'constraints' => [
                    NotBlank::class => 'Please enter an indicator title',
                ]
            ],
            [
                'title' => 'Address',
                'type' => TextType::class,
                'field' => 'address',
                'width' => 200,
            ],
            [
                'title' => 'Transaction',
                'type' => TextType::class,
                'field' => 'transaction',
                'width' => 150,
            ],
            [
                'title' => 'IP',
                'type' => TextType::class,
                'field' => 'ip',
                'width' => 100,
                'constraints' => [
                    IpFormat::class => 'Please enter a valid IP address',
                ]
            ],
            [
                'title' => 'Description',
                'type' => TextType::class,
                'field' => 'description',
                'hidden' => true,
            ],
            [
                'title' => 'Tags',
                'type' => CollectionType::class,
                'field' => 'tags',
                'hidden' => true,
            ],
        ];
    }

    /**
     * @return array
     */
    private function getCategoriesList(): array
    {
        return array_map(function (Category $row) {
            return [
                'value' => $row->getId(),
                'label' => $row->getTitle(),
            ];
        }, $this->em->getRepository(Category::class)->findAll());
    }

    #[Route('/indicators', name: 'app_indicators')]
    public function index(): Response
    {
        return $this->render('indicator/index.html.twig', [
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    #[Route('/indicators/list', name: 'app_indicators_list')]
    public function getRows(Request $request): JsonResponse
    {
        $rows = $this->em->getRepository(Indicator::class)->findAll();

        $data = array_map(function (Indicator $row) {

            $category = $row->getCategory();

            return [
                'id' => $row->getId(),
                'category' => [
                    'id' => $category ? $category->getId() : null,
                    'label' => $category ? $category->getTitle() : null,
                ],
                'title' => $row->getTitle(),
                'address' => $row->getAddress(),
                'transaction' => $row->getTransaction(),
                'ip' => $row->getIp(),
                'description' => $row->getDescription(),
                'tags' => $row->getTags(),
            ];
        }, $rows);

        return $this->httpService->response($data);
    }

    /**
     * Create an indicator
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/indicators/add', name: 'app_indicators_add')]
    public function addRow(Request $request): Response
    {
        return $this->render('indicator/add.html.twig', [
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    /**
     * Edit an indicator
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/indicators/create', name: 'app_indicators_create', methods: ['PUT'])]
    public function createRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        try {
            $entity = $this->tableBuilder->create(Indicator::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($entity->getId(), true, "Indicator successfully created", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/indicators/edit', name: 'app_indicators_edit', methods: ['POST'])]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        try {
            $entity = $this->tableBuilder->save(Indicator::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($data['id'], true, "Indicator successfully edited", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
    }

    /**
     * Delete an indicators
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/indicators/delete', name: 'app_indicators_delete')]
    public function deleteRow(Request $request): JsonResponse
    {
        $id = (int)$request->getContent(false);

        $row = $this->em->getRepository(Indicator::class)->find($id);
        $row->setRemoved(true);
        $this->em->persist($row);
        $this->em->flush();

        return $this->httpService->response($id, true, "Indicator successfully deleted");
    }
}
