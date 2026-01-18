<?php

namespace App\Traits;

use App\Utils\Services\E2EEService;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

trait ApiResponseTrait
{
    /**
     * The current path of resource to respond
     *
     * @var string
     */
    protected string $resourceItem;

    /**
     * The current path of collection resource to respond
     *
     * @var string
     */
    protected string $resourceCollection;

    /**
     * @param string $message
     * @param $data
     * @param $code
     * @return JsonResponse
     */
    protected function respondWithCustomData(string $message, $data, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ];
        return new JsonResponse($response, $code);
    }

    protected function responseWithError(string $message,  $error, int|string $code = 200): JsonResponse
    {
        $response = [
            'message' => $message,
            'code' => $code,
            'error' => $error,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ];
        return new JsonResponse($response, $code);
    }


    protected function getTimestampInMilliseconds(): int
    {
        return intdiv((int)now()->format('Uu'), 1000);
    }

    /**
     *
     * Return no content for delete requests
     */
    protected function respondWithNoContent(): JsonResponse
    {
        return $this->respondWithE2EE([
            'data' => null,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     *
     * Return collection response from the application
     */
    protected function respondWithCollection(LengthAwarePaginator|CursorPaginator|Collection $collection, $additionalData = [])
    {
        // If additionalData is a string, treat it as a message
        if (is_string($additionalData)) {
            $additionalData = ['message' => $additionalData];
        }

        // Ensure additionalData is an array
        if (!is_array($additionalData)) {
            $additionalData = [];
        }

        $data = (new $this->resourceCollection($collection))->additional(
            ['meta' => ['timestamp' => $this->getTimestampInMilliseconds()], ...$additionalData]
        );
        return $this->respondWithE2EE($data);
    }

    /**
     *
     * Return single item response from the application
     */
    protected function respondWithItem(Model|array $item, $additionalData = []): mixed
    {
        $data = (new $this->resourceItem($item))->additional(
            [...$additionalData, 'meta' => ['timestamp' => $this->getTimestampInMilliseconds()]]
        );
        return $this->respondWithE2EE($data);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function toMeta(Request $request): array
    {
        return [
            'copyright' => 'Copyright ' . date('Y') . ' ' . env('app_name', 'TDR Backend Engine'),
            'timestamp' => $this->getTimestampInMilliseconds()
        ];
    }


    protected function respondWithPlain(array $response, int $code = 200): JsonResponse
    {
        return new JsonResponse($response, $code);
    }
}
