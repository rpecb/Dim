<?php
/**
 * Dim - the PHP dependency injection manager.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 *
 * @author    Dmitry Gres <dm.gres@gmail.com>
 * @copyright 2014 Dmitry Gres
 * @link      https://github.com/GR3S/Dim
 * @license   https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @version   1.0.0
 * @package   Dim
 */

namespace Dim\Service;

use Dim\Container;
use Dim\Service;

/**
 * Service which instance is the result of function.
 *
 * @package Dim
 * @author  Dmitry Gres <dm.gres@gmail.com>
 * @license https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @link    https://github.com/GR3S/Dim/blob/master/src/Dim/Service/Factory.php
 */
class Factory extends Service
{
    /**
     * A function that creates an instance of the service.
     *
     * @var callable
     */
    protected $callable;

    /**
     * Instantiates the service.
     *
     * @param string $class The class name for creating service.
     * @param callable $callable A function that creates an instance of the service.
     * @param mixed $arguments An argument or an array of arguments that will be passed to the service.
     * @throws \InvalidArgumentException If function is not callable.
     */
    public function __construct($class, $callable, $arguments = null)
    {
        parent::__construct($class, $arguments);
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('A callable expected.');
        }
        $this->callable = $callable;
    }

    /**
     * Creates an instance of the class of the service.
     *
     * @param mixed $arguments An argument or an array of arguments that will be passed to the service.
     * @param Container $dim An instance of the dependency injection container.
     * @return object An instance of the class of the service.
     */
    public function get($arguments = null, Container $dim = null)
    {
        return static::resolveCallable($this->callable, (array)$arguments + $this->arguments, $dim);
    }
}