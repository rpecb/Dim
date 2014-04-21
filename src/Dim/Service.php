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
 * Service that instantiates a class.
 *
 * @package Dim
 * @author  Dmitry Gres <dm.gres@gmail.com>
 * @license https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @link    https://github.com/GR3S/Dim/blob/master/src/Dim/Service.php
 */
class Service implements ServiceInterface
{
    /**
     * The class name for creating service.
     *
     * @var string
     */
    protected $class;

    /**
     * Arguments that will be passed to the service.
     *
     * @var array
     */
    protected $arguments;

    /**
     * Instantiates the service.
     *
     * @param  string $class The class name for creating service.
     * @param  mixed $arguments An argument or an array of arguments that will be passed to the
     *                                              service.
     * @throws \InvalidArgumentException If the class does not exists.
     */
    public function __construct($class, $arguments = null)
    {
        if (!is_string($class) || !class_exists($class)) {
            throw new \InvalidArgumentException('A class name expected.');
        }
        $this->class = $class;
        $this->arguments = (array)$arguments;
    }

    /**
     * Returns service class name.
     *
     * @return string Service class name.
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Creates an instance of the class of the service.
     *
     * @param  mixed $arguments An argument or an array of arguments that will be passed to the service.
     * @param  Container $dim An instance of the dependency injection container.
     * @return object    An instance of the class of the service.
     */
    public function get($arguments = null, Container $dim = null)
    {
        return static::resolveClass($this->class, (array)$arguments + $this->arguments, $dim);
    }

    /**
     * Creates an instance of the class of the service.
     *
     * @param  mixed $arguments An argument or an array of arguments that will be passed to the service.
     * @param  Container $dim An instance of the dependency injection container.
     * @return object    An instance of the class of the service.
     */
    public function __invoke($arguments = null, Container $dim = null)
    {
        return $this->get($arguments, $dim);
    }

    /**
     * Resolves dependencies of the class and creates an instance of the class of the service.
     *
     * @param  string $class The class name for creating service.
     * @param  array $arguments Arguments that will be passed to the service.
     * @param  Container $dim An instance of the dependency injection container.
     * @return object                    An instance of the class of the service.
     * @throws \InvalidArgumentException If the class is not instantiable.
     */
    protected static function resolveClass($class, array $arguments = array(), Container $dim = null)
    {
        $reflectionClass = new \ReflectionClass($class);
        if (!$reflectionClass->isInstantiable()) {
            throw new \InvalidArgumentException($class . ' class is not instantiable.');
        }
        $reflectionMethod = $reflectionClass->getConstructor();
        if ($reflectionMethod) {
            return $reflectionClass->newInstanceArgs(
                static::getReflectionParameters($reflectionMethod, $arguments, $dim)
            );
        }

        return $reflectionClass->newInstance();
    }

    /**
     * Resolves dependencies of the callable and creates an instance of the class of the service.
     *
     * @param  callable $callable A function that creates an instance of the service.
     * @param  array $arguments Arguments that will be passed to the service.
     * @param  Container $dim An instance of the dependency injection container.
     * @return object                    An instance of the class of the service.
     * @throws \InvalidArgumentException If the callable is non-public.
     */
    protected static function resolveCallable($callable, array $arguments = array(), Container $dim = null)
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
            $reflection = new \ReflectionMethod($class, $method);
            if (!$reflection->isPublic()) {
                throw new \InvalidArgumentException(
                    'Can not access to non-public method ' .
                    (is_object($class) ? get_class($class) : $class) . '::' . $method . '.'
                );
            }
        } else {
            $reflection = new \ReflectionFunction($callable);
        }

        return call_user_func_array($callable, static::getReflectionParameters($reflection, $arguments, $dim));
    }

    /**
     * Returns arguments for creating an instance of the service.
     * Adds to passed arguments default values ​and dependencies.
     *
     * @param  \ReflectionFunctionAbstract $reflection An instance of the reflection of the function.
     * @param  array $arguments Arguments that will be passed to the service.
     * @param  Container $dim An instance of the dependency injection container.
     * @return array                       Passed arguments with default values and dependencies.
     * @throws \BadMethodCallException     If service is not registered in the dependency injection container or if
     *                                     insufficient number of arguments passed or if can not satisfy service
     *                                     dependencies.
     */
    protected static function getReflectionParameters(
        \ReflectionFunctionAbstract $reflection,
        array $arguments = array(),
        Container $dim = null
    ) {
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
                    throw new \BadMethodCallException('Not enough arguments.');
                }
                $parameters[] = $dim->get($classReflection->getName());
            }
        }

        return $parameters ? $parameters : $arguments;
    }
}
