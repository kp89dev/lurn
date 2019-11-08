<?php
namespace App\Jobs\Infusionsoft;

use Illuminate\Support\Facades\Log;

trait ResponseHandlerTrait
{
    private $error;
    private $exception;
    private $response;

    public function getError()
    {
        if ($this->error) {
            return $this->error;
        }

        if (method_exists($this, 'handleResponse')) {
            $this->handleResponse();
        }

        return $this->error;
    }

    public function hasError() : bool
    {
        if (method_exists($this, 'handleResponse')) {
            $this->handleResponse();
        }
        
        return null !== $this->error;
    }

    public function handleException(\Exception $e, string $customMessage = null)
    {
        if ($e->getMessage() !== 'An error message') {
            Log::error($e);
        }
        $this->exception = $e;
        $this->error = $customMessage;

        if (null === $this->error) {
            $this->error = 'There was a problem placing your order. ' .
                'Please review your data and submit again. If the problems persists please contact support.';
        }

        return $this;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
