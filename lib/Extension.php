<?php

class Extension extends Factory
{
    protected $service;

    public function __construct(Service $service, $callable, $arguments = null)
    {
        parent::__construct($service->getClass(), $callable, $arguments);
        $this->service = $service;
    }

    public function get($arguments = null, Dim $dim = null)
    {
        return static::resolveCallable(
            $this->callable,
            (array)$arguments + $this->arguments + array($this->service->get()),
            $dim
        );
    }
} 