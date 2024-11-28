<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends AbstractController
{
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
     * @param Request $request
     * @return bool
     */
    protected function isEdit(Request $request): bool
    {
        return $request->getMethod() === "POST";
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isCreate(Request $request): bool
    {
        return $request->getMethod() === "PUT";
    }
}
