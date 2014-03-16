<?php

class Dim implements ArrayAccess
{
    // TODO: Unit tests: stdClass, service interface, break dependency
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
        $this->instance($this, get_called_class());
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

    public function add($class, $names = null, $arguments = array())
    {
        $scope = & $this->getScope();
        $service = new Service($this, $class, $arguments);
        $scope = array_fill_keys($names ? (array)$names : $this->getNames($class), $service) + $scope;
    }

    public function singleton($class, $names = null, $arguments = array())
    {
        $scope = & $this->getScope();
        $singleton = new Singleton($this, $class, $arguments);
        $scope = array_fill_keys($names ? (array)$names : $this->getNames($class), $singleton) + $scope;
    }

    public function factory($callable, $names, $arguments = array())
    {
        $scope = & $this->getScope();
        $factory = new Factory($this, $callable, $arguments);
        $scope = array_fill_keys((array)$names, $factory) + $scope;
    }

    public function instance($instance, $names = null)
    {
        if (!is_object($instance)) {
            throw new InvalidArgumentException('An instance expected.');
        }
        $scope = & $this->getScope();
        $scope = array_fill_keys($names ? (array)$names : $this->getNames(get_class($instance)), $instance) + $scope;
    }

    public function extend($names, $callable, $arguments = array())
    {
        $names = (array)$names;
        $name = current($names);
        $scope = & $this->getScope();
        if (!array_key_exists($name, $scope)) {
            throw new InvalidArgumentException('Dependency ' . $name . ' is not defined in current scope.');
        }
        $extended = new Extended($this, $scope[$name], $callable, $arguments);
        $scope = array_fill_keys($names, $extended) + $scope;
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
        $result = $value instanceof Service ? $value($arguments) : $value;
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
        $scope = & $this->getScope();
        $scope = array();
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
        // @codeCoverageIgnoreStart
        if (function_exists('class_uses')) {
            $names += class_uses($class);
        }
        // @codeCoverageIgnoreEnd
        $names[] = $class;
        return $names;
    }

    public function offsetSet($name, $class)
    {
        $this->add($class, $name);
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

    public function __set($name, $class)
    {
        $this->add($class, $name);
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