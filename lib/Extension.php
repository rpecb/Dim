<?php

class Extension extends Factory
{
    protected $service;

    public function __construct(Service $service, $callable, $arguments = array()) #
    {
        parent::__construct($service->getClass(), $callable, $arguments);
        $this->service = $service;
    }

    public function get($arguments = array()) #
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this->resolveCallable($this->callable, $arguments + $this->arguments + array($this->service->get()));
    }
} 