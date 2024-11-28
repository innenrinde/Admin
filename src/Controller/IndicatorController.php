<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Indicator;
use App\Services\HttpService;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly HttpService $httpService
    ) {
        $this->columns = [
            [
                'title' => 'ID',
                'type' => 'number',
                'field' => 'id',
                'isPk' => true,
            ],
            [
                'title' => 'Category',
                'type' => 'select',
                'field' => 'category',
                'options' => $this->getCategoriesList(),
            ],
            [
                'title' => 'Title',
                'type' => 'string',
                'field' => 'title',
            ],
            [
                'title' => 'Address',
                'type' => 'string',
                'field' => 'address',
            ],
            [
                'title' => 'Transaction',
                'type' => 'string',
                'field' => 'transaction',
            ],
            [
                'title' => 'IP',
                'type' => 'string',
                'field' => 'ip',
            ],
            [
                'title' => 'Description',
                'type' => 'string',
                'field' => 'description',
                'hidden' => true,
            ],
            [
                'title' => 'tags',
                'type' => 'string',
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
            'columns' => $this->columns
        ]);
    }

    #[Route('/indicators/list', name: 'app_indicators_list')]
    public function getRows(Request $request): JsonResponse
    {
        $rows = $this->em->getRepository(Indicator::class)->findAll();

        $data = array_map(function (Indicator $row) {
            return [
                'id' => $row->getId(),
                'category' => $row->getCategory() ? $row->getCategory()->getTitle() : '',
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
            'columns' => $this->columns,
        ]);
    }

    /**
     * Create an user
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/indicators/edit', name: 'app_indicators_edit')]
    public function saveRow(Request $request): JsonResponse
    {
        $data = $request->toArray();

        if ($this->isCreate($request)) {
            $data['id'] = 0;
        } else if (!$data['id']) {
            throw new \Error("payload mismatch");
        }

        if (!isset($data['title'])) {
            return $this->httpService->response($data['id'], false, "Indicator title missing");
        }

        if (!isset($data['category'])) {
            return $this->httpService->response($data['id'], false, "Select a category");
        }

        $row = new Indicator();
        if ($this->isEdit($request)) {
            $row = $this->em->getRepository(Indicator::class)->find($data['id']);
        }

        $row->setTitle($data['title'] ?? "");
        $row->setAddress($data['address'] ?? "");
        $row->setTransaction($data['transaction'] ?? "");
        $row->setIp($data['ip'] ?? "");
        $row->setDescription($data['description'] ?? "");

        $tags = isset($data['tags']) && !is_array($data['tags']) ? explode(',', $data['tags']) : [];
        $row->setTags($tags);

        $category = $this->em->getRepository(Category::class)->find($data['category']);
        $row->setCategory($category);

        $this->em->persist($row);
        $this->em->flush();

        $message = $this->isEdit($request) ? "Indicator successfully edited" : "Indicator successfully created";

        return $this->httpService->response($data['id'], true, $message, $data);
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
