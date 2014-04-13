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
use Dim\ServiceInterface;

/**
 * Class Extension
 * @package Dim\Service
 */
class Extension extends Factory
{
    /**
     * @var \Dim\Service
     */
    protected $service;

    /**
     * @param ServiceInterface $service
     * @param null $callable
     * @param null $arguments
     */
    public function __construct(ServiceInterface $service, $callable, $arguments = null)
    {
        parent::__construct($service->getClass(), $callable, $arguments);
        $this->service = $service;
    }

    /**
     * @param null $arguments
     * @param Container $dim
     * @return mixed
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