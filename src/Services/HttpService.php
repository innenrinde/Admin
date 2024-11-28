<?php

namespace App\Services;

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

        if ($function_name === "httpResponse") {
            if ($count === 1) {
                return HttpService::httpResponseContent(...$arguments);
            } else if ($count === 3 || $count === 4) {
                return HttpService::httpResponseWithMessage(...$arguments);
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
    static function httpResponseWithMessage(int $pk, bool $isSuccess, string $message, array $content = null): JsonResponse
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
     * @return JsonResponse
     */
    static function httpResponseContent(array $content): JsonResponse
    {
        return new JsonResponse([ 'content' => $content ], Response::HTTP_OK);
    }
}