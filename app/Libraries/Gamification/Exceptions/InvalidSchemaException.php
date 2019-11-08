<?php

namespace Gamification\Exceptions;

use Exception;

class InvalidSchemaException extends Exception
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param  Validator $validator
     * @param  int $code
     * @param  Exception $previous
     */
    public function __construct($validator, $code = 0, Exception $previous = null)
    {
        parent::__construct(implode("\n", $validator->errors()->all()), $code, $previous);

        $this->validator = $validator;
    }

    /**
     * Return a string representation of the object.
     * 
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    /**
     * Return the validator associated with the exception.
     * 
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }
}