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
 * Class Factory
 * @package Dim\Service
 */
class Factory extends Service
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param $class
     * @param null $callable
     * @param null $arguments
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
     * @param null $arguments
     * @param Container $dim
     * @return mixed
     */
    public function get($arguments = null, Container $dim = null)
    {
        return static::resolveCallable($this->callable, (array)$arguments + $this->arguments, $dim);
    }
}