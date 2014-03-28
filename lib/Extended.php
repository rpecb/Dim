<?php

class Extended extends Factory
{
    protected $value;

    public function __construct($value, $callable, $arguments = array())
    {
        parent::__construct($callable, $arguments);
        $this->value = $value;
    }

    public function get($arguments = array())
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        $value = $this->value instanceof Service ? $this->value->get() : $this->value;
        return $this->resolveCallable($this->callable, $arguments + $this->arguments + array($value));
    }
} 