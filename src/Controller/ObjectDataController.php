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
                'width' => 'w30',
                'constraints' => [
                    NotBlank::class => 'Please select a category',
                ]
            ],
            [
                'title' => 'Title',
                'type' => TextType::class,
                'field' => 'title',
                'width' => 'w30',
                'constraints' => [
                    NotBlank::class => 'Please enter a title',
                ]
            ],
            [
                'title' => 'Credit line',
                'type' => TextType::class,
                'field' => 'info1',
                'width' => 'w30',
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
        return $this->render('objectdata/index.html.twig', [
            'columns' => $this->tableBuilder->getColumns($this->columns),
        ]);
    }

    #[Route('/objectdata/list', name: 'app_objectdata_list')]
    public function getRows(Request $request): JsonResponse
    {
        $rows = $this->em->getRepository(ObjectData::class)->findAll();

        $data = array_map(function (ObjectData $row) {

            $category = $row->getCategory();

            return [
                'id' => $row->getId(),
                'category' => [
                    'value' => $category ? $category->getId() : null,
                    'label' => $category ? $category->getTitle() : null,
                ],
                'title' => $row->getTitle(),
                'imageUrl' => $row->getImageUrl(),
                'info1' => $row->getInfo1(),
//                'address' => $row->getAddress(),
//                'transaction' => $row->getTransaction(),
//                'ip' => $row->getIp(),
//                'description' => $row->getDescription(),
//                'tags' => $row->getTags(),
            ];
        }, $rows);

        return $this->httpService->response($data);
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
