<?php
/**
 * Dim - the PHP dependency injection manager.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 *
 * @package   Dim
 * @author    Dmitry Gres <dm.gres@gmail.com>
 * @copyright 2014 Dmitry Gres
 * @license   https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @version   1.0.0
 * @link      https://github.com/GR3S/Dim
 */

namespace Dim;

/**
 * Dependency injection container.
 *
 * @package Dim
 * @author  Dmitry Gres <dm.gres@gmail.com>
 * @license https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @link    https://github.com/GR3S/Dim/blob/master/src/Dim/Container.php
 */
class Container implements \ArrayAccess
{
    /**
     * Dependencies.
     *
     * @var array
     */
    protected $values = array();

    /**
     * List of nested scopes.
     *
     * @var \SplDoublyLinkedList
     */
    protected $scopes;

    /**
     * Instantiates the container.
     */
    public function __construct()
    {
        $this->scopes = new \SplDoublyLinkedList;
        $this->scopes->setIteratorMode(\SplDoublyLinkedList::IT_MODE_DELETE);
        $this->set($this, get_called_class());
    }

    /**
     * Sets current scope.
     *
     * @param  string $scope Scope name.
     * @param  null|callable $callable A callable that can do multiple operations in current scope.
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function scope($scope, $callable = null)
    {
        $this->scopes->push('__' . $scope . '__');
        if ($callable) {
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException('A callable expected.');
            }
            $mode = $this->scopes->getIteratorMode();
            $this->scopes->setIteratorMode(\SplDoublyLinkedList::IT_MODE_KEEP);
            $callable();
            $this->scopes = new \SplDoublyLinkedList;
            $this->scopes->setIteratorMode($mode);
        }

        return $this;
    }

    /**
     * Sets a dependency.
     *
     * @param  mixed $service A parameter or a service.
     * @param  null|string|array $names A name or an array of names of the dependency.
     * @throws \BadMethodCallException If a name of the dependency does not specified.
     */
    public function set($service, $names = null)
    {
        $scope = & $this->getScope();
        $names = (array)$names;
        if (!$names) {
            if ($service instanceof ServiceInterface) {
                $names = static::getNames($service->getClass());
            } elseif (is_object($service)) {
                $names = static::getNames(get_class($service));
            } else {
                throw new \BadMethodCallException('A name of the dependency does not specified.');
            }
        }
        $scope = array_fill_keys($names, $service) + $scope;
    }

    /**
     * Sets an alias of dependency.
     *
     * @param  string $name A Name of the dependency.
     * @param  string|array $aliases An alias or an array of aliases of the dependency.
     * @throws \InvalidArgumentException If dependency with this name is not defined in current scope.
     */
    public function alias($name, $aliases)
    {
        $scope = & $this->getScope();
        if (!array_key_exists($name, $scope)) {
            throw new \InvalidArgumentException('Dependency ' . $name . ' is not defined in current scope.');
        }
        $scope = array_fill_keys((array)$aliases, $scope[$name]) + $scope;
    }

    /**
     * Returns the dependency.
     *
     * @param  string $name A name of the dependency.
     * @param  mixed $arguments An argument or an array of arguments that will be passed to dependency.
     * @return mixed  The dependency value.
     */
    public function get($name, $arguments = null)
    {
        $mode = $this->scopes->getIteratorMode();
        $this->scopes->setIteratorMode(\SplDoublyLinkedList::IT_MODE_KEEP);
        $value = $this->raw($name);
        $result = $value instanceof ServiceInterface ? $value->get($arguments, $this) : $value;
        $this->scopes = new \SplDoublyLinkedList;
        $this->scopes->setIteratorMode($mode);

        return $result;
    }

    /**
     * Returns a raw value from the container.
     *
     * @param  string $name A name of dependency.
     * @return mixed                     The raw value from the container.
     * @throws \InvalidArgumentException If dependency with this name is not defined in current scope.
     */
    public function raw($name)
    {
        $scope = & $this->getScope();
        if (!array_key_exists($name, $scope)) {
            throw new \InvalidArgumentException('Dependency ' . $name . ' is not defined in current scope.');
        }

        return $scope[$name];
    }

    /**
     * Checks that the dependency is defined.
     *
     * @param  string $name A name of the dependency.
     * @return bool   Whether the dependency is defined.
     */
    public function has($name)
    {
        $scope = & $this->getScope();

        return array_key_exists($name, $scope);
    }

    /**
     * Removes the dependency from the container.
     *
     * @param string $name A name of the dependency.
     */
    public function remove($name)
    {
        $scope = & $this->getScope();
        unset($scope[$name]);
    }

    /**
     * Removes all dependencies from the container.
     */
    public function clear()
    {
        $count = count($this->scopes);
        $scope = & $this->getScope();
        $scope = array();
        if (!$count) {
            $this->set($this, get_called_class());
        }
    }

    /**
     * Returns a reference to part of array with dependencies of current scope.
     *
     * @return array Dependencies of current scope
     */
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

    /**
     * Returns names of all parent classes, interfaces, traits extended by given class.
     *
     * @param  string $class A class name.
     * @return array  Names of parent classes, interfaces, traits extended by given class.
     */
    protected static function getNames($class)
    {
        $names = class_parents($class) + class_implements($class);
        if (function_exists('class_uses')) {
            $names += class_uses($class);
        }
        $names[] = $class;

        return $names;
    }

    /**
     * Sets a dependency.
     *
     * @param string $name A name of the dependency.
     * @param mixed $service A parameter or a service.
     */
    public function offsetSet($name, $service)
    {
        $this->set($service, $name);
    }

    /**
     * Returns the dependency.
     *
     * @param  string $name A name of the dependency.
     * @return mixed  The dependency value.
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Checks that the dependency is defined.
     *
     * @param  string $name A name of the dependency.
     * @return bool   Whether the dependency is defined.
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * Removes the dependency from the container.
     *
     * @param string $name A name of the dependency.
     */
    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    /**
     * Sets a dependency.
     *
     * @param string $name A name of the dependency.
     * @param mixed $service A parameter or a service.
     */
    public function __set($name, $service)
    {
        $this->set($service, $name);
    }

    /**
     * Returns the dependency.
     *
     * @param  string $name A name of the dependency.
     * @return mixed  The dependency value.
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Checks that the dependency is defined.
     *
     * @param  string $name A name of the dependency.
     * @return bool   Whether the dependency is defined.
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Removes the dependency from the container.
     *
     * @param string $name A name of the dependency.
     */
    public function __unset($name)
    {
        $this->remove($name);
    }

    /**
     * Returns the dependency.
     *
     * @param  string $name A name of the dependency.
     * @param  mixed $arguments An argument or an array of arguments that will be passed to dependency.
     * @return mixed  The dependency value.
     */
    public function __invoke($name, $arguments = null)
    {
        return $this->get($name, $arguments);
    }
}
