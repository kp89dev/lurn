<?php

namespace App\Api\Http\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\MessageBag;

/**
 * trait ApiResponseHelper.
 */
trait ApiResponseHelper
{
    /**
     * @var int
     */
    protected $statusCode = IlluminateResponse::HTTP_OK;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var MessageBag
     */
    protected $messages;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * @param int $statusCode
     *
     * @return $this
     */
    protected function statusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusOk()
    {
        $this->statusCode = IlluminateResponse::HTTP_OK;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusNotFound()
    {
        $this->statusCode = IlluminateResponse::HTTP_NOT_FOUND;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusFailedValidation()
    {
        $this->statusCode = IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusBadRequest()
    {
        $this->statusCode = IlluminateResponse::HTTP_BAD_REQUEST;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusUnauthorized()
    {
        $this->statusCode = IlluminateResponse::HTTP_UNAUTHORIZED;

        return $this;
    }

    protected function statusForbidden()
    {
        $this->statusCode = IlluminateResponse::HTTP_FORBIDDEN;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusInternalError()
    {
        $this->statusCode = IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusCreated()
    {
        $this->statusCode = IlluminateResponse::HTTP_CREATED;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusUpdated()
    {
        $this->statusCode = IlluminateResponse::HTTP_RESET_CONTENT;

        return $this;
    }

    /**
     * @return $this
     */
    protected function statusDeleted()
    {
        $this->statusCode = IlluminateResponse::HTTP_NO_CONTENT;

        return $this;
    }

    /**
     * Add message in MessageBag.
     *
     * @return $this
     */
    protected function messages($key, $message = null)
    {
        if (!$this->messages) {
            $this->messages = new MessageBag();
        }

        if ($message) {
            $this->messages->add($key, $message);
        } else {
            $this->messages->merge($key);
        }

        return $this;
    }

    /**
     * Add message in MessageBag.
     *
     * @return $this
     */
    protected function message($key, $message = null)
    {
        if (!$message) {
            $message = $key;
            $key = 0;
        }

        $this->messages($key, $message);

        return $this;
    }

    public function paginator(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @param array|LengthAwarePaginator $data
     *
     * @return $this
     */
    protected function data($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            $this->paginator($data);
            $data = $data->getCollection()->toArray();
        }

        $this->data = $data;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return IlluminateResponse
     */
    protected function respond($headers = [])
    {
        $response = [
            'data' => $this->data,
        ];

        if ($this->messages && $this->messages->count()) {
            $response['messages'] = $this->messages;
        }

        if ($this->paginator) {
            $response['paginator'] = array_except($this->paginator->toArray(), 'data');
        }

        return new IlluminateResponse($response, $this->statusCode, $headers);
    }

    /**
     * @param array $headers
     *
     * @return IlluminateResponse
     */
    protected function respondWithError($headers = [])
    {
        $response = [
            'error' => $this->messages->toArray()[0],
        ];

        return new IlluminateResponse($response, $this->statusCode, $headers);
    }
}
