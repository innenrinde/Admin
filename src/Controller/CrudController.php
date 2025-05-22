<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends AbstractController
{
    protected function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @return Response
     */
    abstract public function index(): Response;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function getRows(Request $request): JsonResponse;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function saveRow(Request $request): JsonResponse;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function deleteRow(Request $request): JsonResponse;

    /**
     * Custom date time format
     * @param \DateTime|null $date
     * @return string
     */
    protected function dateFormat(?\DateTime $date): string
    {
        if ($date) {
            return $date->format("Y-m-d H:i:s");
        }

        return "";
    }

    /**
     * @param string $entity
     * @param array $params
     * @return array
     */
    protected function filteredRows(string $entity, array $params): array
    {
        $page = isset($params['page']) ? intval($params['page']) : 0;
        $limit = isset($params['limit']) && $params['limit'] ? intval($params['limit']) : 500;

        $rows = $this->em->getRepository($entity)->findBy([], null, $limit, $page*$limit);
        $total = $this->em->getRepository($entity)->count();

        return [
            'rows' => $rows,
            'pager' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
            ],
        ];
    }
}
