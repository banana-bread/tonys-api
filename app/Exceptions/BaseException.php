<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BaseException extends Exception
{
    public    $uuid;
    protected $httpStatus = 500;

    protected $errorType = 'api';
    protected $errorCode = 'exception';
    protected $errorData = [];

    protected $debugMessage;
    protected $publicMessage = 'A server error has occurred.';

    /**
     * Create a new BaseException instance.
     *
     * @param array $errorData
     * @param string $debugMessage
     */
    public function __construct(array $errorData = [], string $debugMessage = null, Throwable $previous = null)
    {
        $this->debugMessage = $debugMessage ?? $this->debugMessage;
        $this->errorData = $errorData;

        parent::__construct($this->publicMessage, 0, $previous);  

        $this->addErrorData('exception', get_class($this));
        $this->addErrorData('file', $this->getFile().'('.$this->getLine().')');
    }

    /**
     * @param Throwable $source
     * @param array $errorData
     * @param string $debugMessage
     * @return BaseException
     */
    public static function fromException(Throwable $source, array $errorData = [], string $debugMessage = null)
    {
        $exception = new self($errorData, $debugMessage, $source);
        $exception->uuid = $source->uuid;
        $exception->publicMessage = $source->getMessage();
        $exception->addErrorData('source', get_class($source));
        $exception->addErrorData('source_file', $source->getFile().'('.$source->getLine().')');
        return $exception;
    }

    /**
     * Get the Http status code for the exception instance.
     *
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Get the broad error category for the exception instance.
     *
     * @return string
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * Get the specific error code for the exception instance.
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Add error data to the exception instance.
     *
     * @param null $key
     * @param null $value
     * @return array
     */
    public function addErrorData($key = null, $value = null)
    {
        if ( is_string($key) )
        {
            $this->errorData[$key] = $value;
        }
        else if ( is_array($key) )
        {
            $this->errorData = array_merge($this->errorData, $key);
        }
    }

    /**
     * Get the error data for the exception instance.
     *
     * @return array
     */
    public function getErrorData()
    {
        return $this->errorData;
    }

    /**
     * Get the developer message for the exception instance.
     *
     * @return string
     */
    public function getDebugMessage()
    {
        return $this->debugMessage;
    }

    /**
     * Get the user message for the exception instance.
     *
     * @return string
     */
    public function getPublicMessage()
    {
        return $this->publicMessage;
    }

    public function toArray()
    {
        return [
            'type'              => $this->errorType,
            'code'              => $this->errorCode,
            'data'              => $this->errorData,
            'debugMessage'      => $this->debugMessage,
            'publicMessage'     => $this->publicMessage,
            'file'              => $this->getFile(),
            'line'              => $this->getLine(),
            'trace'             => $this->getTrace(),
        ];
    }
}
