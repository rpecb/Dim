<?php

class Dim implements ArrayAccess
{
    // TODO: Unit tests: with scopes and arguments, update depends
    // TODO: namespaces
    // TODO: Doc blocks
    // TODO: PHP CS Fixer
    // TODO: Static analysis
    // TODO: Security analysis
    // TODO: Composer package
    // TODO: Post on Github
    protected $values = array();
    protected $scopes;

    public function __construct()
    {
        $this->scopes = new SplDoublyLinkedList;
        $this->scopes->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
        $this->set($this, get_called_class());
    }

    public function scope($scope, $callable = null)
    {
        $this->scopes->push('__' . $scope . '__');
        if ($callable) {
            if (!is_callable($callable)) {
                throw new InvalidArgumentException('A callable expected.');
            }
            $this->scopes->setIteratorMode(SplDoublyLinkedList::IT_MODE_KEEP);
            $callable();
            $this->scopes = new SplDoublyLinkedList;
            $this->scopes->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
        }
        return $this;
    }

    public function set($service, $names = null)
    {
        $scope = & $this->getScope();
        $names = (array)$names;
        if (!$names) {
            if ($service instanceof Service) {
                $names = $this->getNames($service->getClass());
            } elseif (is_object($service)) {
                $names = $this->getNames(get_class($service));
            } else {
                throw new BadMethodCallException('Service name does not specified.');
            }
        }
        $scope = array_fill_keys($names, $service) + $scope;
    }

    public function alias($name, $aliases)
    {
        $scope = & $this->getScope();
        if (!array_key_exists($name, $scope)) {
            throw new InvalidArgumentException('Dependency ' . $name . ' is not defined in current scope.');
        }
        $scope = array_fill_keys((array)$aliases, $scope[$name]) + $scope;
    }

    public function get($name, $arguments = array())
    {
        $arguments = is_array($arguments) ? $arguments : array($arguments);
        $value = $this->raw($name);
        $result = $value instanceof Service ? $value($arguments, $this) : $value;
        return $result;
    }

    public function raw($name)
    {
        $scope = & $this->getScope();
        if (!array_key_exists($name, $scope)) {
            throw new InvalidArgumentException('Dependency ' . $name . ' is not defined in current scope.');
        }
        return $scope[$name];
    }

    public function has($name)
    {
        $scope = & $this->getScope();
        return array_key_exists($name, $scope);
    }

    public function remove($name)
    {
        $scope = & $this->getScope();
        unset($scope[$name]);
    }

    public function clear()
    {
        $count = count($this->scopes);
        $scope = & $this->getScope();
        $scope = array();
        if (!$count) {
            $this->set($this, get_called_class());
        }
    }

    protected function &getScope()
    {
        $scope = & $this->values;
        foreach ($this->scopes as $v) {
            if (!array_key_exists($v, $scope)) {
                $scope[$v] = array();
            }
            $scope = & $scope[$v];
        }
        return $scope;
    }

    protected function getNames($class)
    {
        $names = class_parents($class) + class_implements($class);
        if (function_exists('class_uses')) {
            $names += class_uses($class);
        }
        $names[] = $class;
        return $names;
    }

    public function offsetSet($name, $service)
    {
        $this->set($service, $name);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }

    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    public function __set($name, $service)
    {
        $this->set($service, $name);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __invoke($name, $arguments = array())
    {
        return $this->get($name, $arguments);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        $this->remove($name);
    }
}