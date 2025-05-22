<?php

namespace App\Services;

use App\Api\HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HttpService
{
    /**
     * @param string $function_name
     * @param array $arguments
     * @return JsonResponse
     */
    public function __call(string $function_name, array $arguments): JsonResponse
    {
        $count = count($arguments);

        if ($function_name === "response") {
            if ($count === 1) {
                $reflection = new \ReflectionClass(get_class($arguments[0]));
                if ($reflection->getShortName() === "HttpResponse") {
                    return $this->httpResponse(...$arguments);
                }
            } else if ($count === 3 || $count === 4) {
                return $this->responseWithMessage(...$arguments);
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
    private function responseWithMessage(int $pk, bool $isSuccess, string $message, array $content = null): JsonResponse
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
     * @param HttpResponse $httpResponse
     * @return JsonResponse
     */
    public function httpResponse(HttpResponse $httpResponse): JsonResponse
    {
        $data = [];

        if ($httpResponse->hasColumns()) {
            $data['columns'] = $httpResponse->getColumns();
        }

        if ($httpResponse->hasRows()) {
            $data['rows'] = $httpResponse->getRows();
        }

        if ($httpResponse->hasPager()) {
            $data['pager'] = $httpResponse->getPager();
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}