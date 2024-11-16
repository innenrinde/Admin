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
     * @param string $function_name
     * @param array $arguments
     */
    public function __call(string $function_name, array $arguments): JsonResponse
    {
        $count = count($arguments);

        if ($function_name === "httpResponse") {
            if ($count === 1) {
                return $this->httpResponseContent(...$arguments);
            } else if ($count === 3 || $count === 4) {
                return $this->httpResponseWithMessage(...$arguments);
            }
        }

        return new JsonResponse();
    }

    /**
     * @param int $pk
     * @param bool $isSuccess
     * @param string $message
     * @param array|null $content
     * @return JsonResponse
     */
    private function httpResponseWithMessage(int $pk, bool $isSuccess, string $message, array $content = null): JsonResponse
    {
        return new JsonResponse([
            'id' => $pk,
            'success' => $isSuccess,
            'message' => $message,
            'content' => $content
        ],
            Response::HTTP_OK);
    }

    /**
     * @param array $content
     */
    private function httpResponseContent(array $content): JsonResponse
    {
        return new JsonResponse([ 'content' => $content ], Response::HTTP_OK);
    }

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
     * @return bool
     */
    protected function isEdit(Request $request): bool
    {
        return $request->getMethod() === "POST";
    }

    /**
     * @return bool
     */
    protected function isCreate(Request $request): bool
    {
        return $request->getMethod() === "PUT";
    }
}
