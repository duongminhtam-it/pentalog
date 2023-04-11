<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Success response method.
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function success(mixed $data = null, string $message = 'Success', int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($response, $code);
    }

    /**
     * Return error response.
     * @param string $message
     * @param int $code
     * @param mixed|null $data
     * @return JsonResponse
     */
    public function error(string $message = 'Error', int $code = Response::HTTP_BAD_REQUEST, mixed $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
}
