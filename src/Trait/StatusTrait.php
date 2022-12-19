<?php

namespace App\Trait;

use Symfony\Component\HttpFoundation\JsonResponse;

trait StatusTrait
{
    /**
     * @param array|string $message
     * @param bool $status
     * @param int $code
     * @return JsonResponse
     */
    public function errorStatus(array|string $message, bool $status = true, int $code = 400): JsonResponse
    {
        $responseData = [
            'status' => $status,
            'message' => $message,
        ];

        return new JsonResponse($responseData, $code);
    }

    /**
     * @param mixed $data
     * @param array $extraParams
     * @param bool $status
     * @return array
     */
    public function serviceStatus(mixed $data = [], array $extraParams = [], bool $status = true): array
    {
        $response = [
            'status' => $status,
            'data' => $data,
        ];

        return array_merge($response, $extraParams);
    }
}