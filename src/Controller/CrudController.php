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
    abstract public function addRow(Request $request): JsonResponse;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function editRow(Request $request): JsonResponse;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function deleteRow(Request $request): JsonResponse;

    /**
     * @param int $pk
     * @param bool $isSuccess
     * @param string $message
     * @param array|null $content
     * @return JsonResponse
     */
    protected function httpResponse(int $pk, bool $isSuccess, string $message, array $content = null): JsonResponse
    {
        return new JsonResponse([
            'id' => $pk,
            'success' => $isSuccess,
            'message' => $message,
            'content' => $content
        ],
            Response::HTTP_OK);
    }
}
