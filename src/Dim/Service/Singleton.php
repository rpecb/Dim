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
 * Class Singleton
 * @package Dim\Service
 */
class Singleton extends Service
{
    /**
     * @var
     */
    protected $instance;

    /**
     * @param null $arguments
     * @param Container $dim
     * @return object
     */
    public function get($arguments = null, Container $dim = null)
    {
        if ($this->instance === null) {
            $this->instance = parent::get($arguments, $dim);
        }
        return $this->instance;
    }
} 