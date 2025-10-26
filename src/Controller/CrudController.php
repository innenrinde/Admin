<?php

namespace App\Controller;

use App\Api\Pager;
use App\Api\Sorting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
     * Get rows based on entity name, sorting options and pagination
     * @param string $entity
     * @param Pager $pager
     * @param Sorting $sorting
     * @return array
     */
    protected function filteredRows(string $entity, Pager $pager, Sorting $sorting): array
    {
        $rows = $this->em->getRepository($entity)->findBy([], [$sorting->sortBy => $sorting->sortDir], $pager->limit, $pager->offset);
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

    /**
     * @param Request $request
     * @return array
     */
    protected function getData(Request $request): array
    {
        return $request->request->all();
    }

    /**
     * Return uploaded files
     * @param Request $request
     * @return array
     */
    protected function getFiles(Request $request): array
    {
        return $request->files->all();
    }

    /**
     * Convert received data to a standard format to be saved into database
     * @param array $data associative data received
     * @param array $columns columns definition to be used
     * @return array
     */
    protected function prepareData(array $data, array $columns): array
    {
        foreach ($columns as $column) {
            if (isset($data[$column['field']])) {
                if ($column['type'] === NumberType::class) {
                    $data[$column['field']] = intval($data[$column['field']]);
                } else if ($column['type'] === ChoiceType::class) {
                    $entity = $this->em->getRepository($column['entity'])->find($data[$column['field']]);
                    $data[$column['field']] = [
                        'value' => $entity ? $entity->getId() : null,
                        'label' => $entity ? $entity->getTitle() : null,
                    ];
                }
            }
        }

        return $data;
    }
}
