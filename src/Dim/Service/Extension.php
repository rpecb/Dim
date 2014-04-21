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

namespace Dim\Service;

use Dim\Container;
use Dim\ServiceInterface;

/**
 * Service that extends other service.
 *
 * @package Dim
 * @author  Dmitry Gres <dm.gres@gmail.com>
 * @license https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @link    https://github.com/GR3S/Dim/blob/master/src/Dim/Service/Extension.php
 */
class Extension extends Factory
{
    /**
     * Extended service.
     *
     * @var ServiceInterface
     */
    protected $service;

    /**
     * Instantiates the service.
     *
     * @param ServiceInterface $service Service that will be extended.
     * @param callable $callable A function that extends an instance of the service.
     * @param mixed $arguments An argument or an array of arguments that will be passed to the service.
     */
    public function __construct(ServiceInterface $service, $callable, $arguments = null)
    {
        parent::__construct($service->getClass(), $callable, $arguments);
        $this->service = $service;
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
        return static::resolveCallable(
            $this->callable,
            (array)$arguments + $this->arguments + array($this->service->get()),
            $dim
        );
    }
}
