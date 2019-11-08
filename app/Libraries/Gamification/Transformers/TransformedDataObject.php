<?php

namespace Gamification\Transformers;

class TransformedDataObject
{
    /**
     * 
     * @var string
     */
    protected $endpoint;

    /**
     * 
     * @var array
     */
    protected $params;

    public function __construct($data)
    {
        $this->endpoint = $data['endpoint'];
        $this->params = $data['params'];
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getParams()
    {
        return $this->params;
    }
}