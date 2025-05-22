<?php

namespace App\Controller;

use App\Builder\Constraints\NotBlank;
use App\Builder\Constraints\NullForCreation;
use App\Builder\TableBuilder;
use App\Entity\Category;
use App\Entity\ObjectData;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ObjectDataController extends CrudController
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

        parent::__construct($this->em);

        $this->columns = [
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
                'title' => 'Image',
                'type' => FileType::class,
                'field' => 'imageUrl',
            ],
            [
                'title' => 'Category',
                'type' => ChoiceType::class,
                'entity' => Category::class,
                'field' => 'category',
                'options' => $this->getCategoriesList(),
                'width' => 'w10',
                'constraints' => [
                    NotBlank::class => 'Please select a category',
                ]
            ],
            [
                'title' => 'Title',
                'type' => TextType::class,
                'field' => 'title',
//                'width' => 'w20',
                'constraints' => [
                    NotBlank::class => 'Please enter a title',
                ]
            ],
            [
                'title' => 'Culture',
                'type' => TextType::class,
                'field' => 'culture',
                'width' => 'w10',
            ],
            [
                'title' => 'Year',
                'type' => TextType::class,
                'field' => 'objectBeginDate',
                'width' => 'w10',
            ],
            [
                'title' => 'Dimensions',
                'type' => TextType::class,
                'field' => 'dimensions',
                'width' => 'w20',
            ],
            [
                'title' => 'Credit line',
                'type' => TextType::class,
                'field' => 'info1',
                'width' => 'w20',
            ],
//            [
//                'title' => 'Address',
//                'type' => TextType::class,
//                'field' => 'address',
//                'width' => 200,
//            ],
//            [
//                'title' => 'Transaction',
//                'type' => TextType::class,
//                'field' => 'transaction',
//                'width' => 150,
//            ],
//            [
//                'title' => 'IP',
//                'type' => TextType::class,
//                'field' => 'ip',
//                'width' => 100,
//                'constraints' => [
//                    IpFormat::class => 'Please enter a valid IP address',
//                ]
//            ],
//            [
//                'title' => 'Description',
//                'type' => TextType::class,
//                'field' => 'description',
//                'hidden' => true,
//            ],
//            [
//                'title' => 'Tags',
//                'type' => CollectionType::class,
//                'field' => 'tags',
//                'hidden' => true,
//            ],
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

    #[Route('/objectdata', name: 'app_objectdata')]
    public function index(): Response
    {
        return $this->render('objectdata/index.html.twig', []);
    }

    #[Route('/objectdata/list', name: 'app_objectdata_list')]
    public function getRows(Request $request): JsonResponse
    {

        $columns = $this->tableBuilder->getColumns($this->columns);

        $data = $this->filteredRows(ObjectData::class, $request->query->all());

        $rows = array_map(function (ObjectData $row) {
            return [
                'id' => $row->getId(),
                'category' => [
                    'value' => $row->getCategoryId(),
                    'label' => $row->getCategoryTitle(),
                ],
                'title' => $row->getTitle(),
                'imageUrl' => $row->getImageUrl(),
                'info1' => $row->getInfo1(),
                'culture' => $row->getCulture(),
                'objectBeginDate' => $row->getObjectBeginDate() + $row->getObjectEndDate(),
                'dimensions' => $row->getDimensions(),
            ];
        }, $data['rows']);

        return $this->httpService->response($rows, $columns, $data['pager']);
    }

    /**
     * Create a data object
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/objectdata/add', name: 'app_objectdata_add')]
    public function addRow(Request $request): Response
    {
        return $this->render('objectdata/add.html.twig', [
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    /**
     * Edit a data object
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/objectdata/create', name: 'app_objectdata_create', methods: ['PUT'])]
    public function createRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        try {
            $entity = $this->tableBuilder->create(ObjectData::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($entity->getId(), true, "Data object successfully created", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
    }

    /**
     * Edit a data object
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/objectdata/edit', name: 'app_objectdata_edit', methods: ['POST'])]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        try {
            $entity = $this->tableBuilder->save(ObjectData::class, $this->columns, $data);
            $this->em->persist($entity);
            $this->em->flush();

            return $this->httpService->response($data['id'], true, "Object data successfully edited", $data);

        } catch (Exception $e) {
            return $this->httpService->response(0, false, $e->getMessage());
        }
    }

    /**
     * Delete a data object
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/objectdata/delete', name: 'app_objectdata_delete')]
    public function deleteRow(Request $request): JsonResponse
    {
        $id = (int)$request->getContent(false);

        $row = $this->em->getRepository(ObjectData::class)->find($id);
        $row->setRemoved(true);
        $this->em->persist($row);
        $this->em->flush();

        return $this->httpService->response($id, true, "Object data successfully deleted");
    }
}
