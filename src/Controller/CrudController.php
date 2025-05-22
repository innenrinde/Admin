<?php

namespace App\Controller;

use App\Api\Pager;
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
     * @param Pager $pager
     * @return array
     */
    protected function filteredRows(string $entity, Pager $pager): array
    {
        $rows = $this->em->getRepository($entity)->findBy([], null, $pager->limit, $pager->offset);
        $total = $this->em->getRepository($entity)->count();

        return [
            'rows' => $rows,
            'pager' => [
                'total' => $total,
                'page' => $pager->page,
                'limit' => $pager->limit,
            ],
        ];
    }
}
