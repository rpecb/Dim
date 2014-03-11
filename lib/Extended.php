<?php

class Extended extends Factory
{
    protected $value;

    public function __construct(Dim $dim, $value, $callable, $arguments = array())
    {
        parent::__construct($dim, $callable);
        $this->value = $value;
    }

    public function get($arguments = array())
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        $value = $this->value instanceof Service ? $this->value->get() : $this->value;
        return $this->resolveCallable($this->callable, array_merge(array($value), $this->arguments, $arguments));
    }
} 