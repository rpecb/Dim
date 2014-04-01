<?php

class Service
{
    protected $class;
    protected $arguments;

    public function __construct($class, $arguments = array()) #
    {
        if (!is_string($class) || !class_exists($class)) {
            throw new InvalidArgumentException('A class name expected.');
        }
        $this->class = $class;
        $this->arguments = is_array($arguments) ? $arguments : array($arguments);
    }

    public function getClass()
    {
        return $this->class;
    }

    public function get($arguments = array(), Dim $dim = null) #
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        return $this->resolveClass($this->class, $arguments + $this->arguments, $dim);
    }

    public function __invoke($arguments = array(), Dim $dim = null) #
    {
        return $this->get($arguments, $dim);
    }

    protected function resolveClass($class, array $arguments = array(), Dim $dim = null) #
    {
        $reflectionClass = new ReflectionClass($class);
        if (!$reflectionClass->isInstantiable()) {
            throw new InvalidArgumentException($class . ' class is not instantiable.');
        }
        $reflectionMethod = $reflectionClass->getConstructor();
        if ($reflectionMethod) {
            return $reflectionClass->newInstanceArgs(
                $this->getReflectionParameters($reflectionMethod, $arguments, $dim)
            );
        }
        return $reflectionClass->newInstance();
    }

    protected function resolveCallable($callable, array $arguments = array(), Dim $dim = null) #
    {
        if (is_array($callable)) {
            list($class, $method) = $callable;
        } elseif (is_string($callable) && strpos($callable, '::') !== false) {
            list($class, $method) = explode('::', $callable, 2);
        } elseif (method_exists($callable, '__invoke')) {
            $class = $callable;
            $method = '__invoke';
        }
        if (isset($class) && isset($method)) {
            $reflection = new ReflectionMethod($class, $method);
            if (!$reflection->isPublic()) {
                throw new InvalidArgumentException(
                    'Can not access to non-public method ' .
                    (is_object($class) ? get_class($class) : $class) . '::' . $method . '.'
                );
            }
        } else {
            $reflection = new ReflectionFunction($callable);
        }
        return call_user_func_array($callable, $this->getReflectionParameters($reflection, $arguments, $dim));
    }

    protected function getReflectionParameters(
        ReflectionFunctionAbstract $reflection,
        array $arguments = array(),
        Dim $dim = null
    )
    { #
        $parameters = array();
        foreach ($reflection->getParameters() as $reflectionParameter) {
            if (array_key_exists($reflectionParameter->getName(), $arguments)) {
                $parameters[] = $arguments[$reflectionParameter->getName()];
            } elseif (array_key_exists($reflectionParameter->getPosition(), $arguments)) {
                $parameters[] = $arguments[$reflectionParameter->getPosition()];
            } elseif ($reflectionParameter->isDefaultValueAvailable()) {
                $parameters[] = $reflectionParameter->getDefaultValue();
            } else {
                $classReflection = $reflectionParameter->getClass();
                if (!is_object($classReflection) || $dim === null || !$dim->has($classReflection->getName())) {
                    throw new BadMethodCallException('Not enough arguments.');
                }
                $parameters[] = $dim->get($classReflection->getName());
            }
        }
        return $parameters ? $parameters : $arguments;
    }
} 