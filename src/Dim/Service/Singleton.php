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
 * Service always returns the same instance of the class.
 *
 * @package Dim
 * @author  Dmitry Gres <dm.gres@gmail.com>
 * @license https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @link    https://github.com/GR3S/Dim/blob/master/src/Dim/Service/Singleton.php
 */
class Singleton extends Service
{
    /**
     * Instance of the class of the service.
     *
     * @var object
     */
    protected $instance;

    /**
     * Creates an instance of the class of the service.
     *
     * @param mixed $arguments An argument or an array of arguments that will be passed to the service.
     * @param Container $dim An instance of the dependency injection container.
     * @return object An instance of the class of the service.
     */
    public function get($arguments = null, Container $dim = null)
    {
        if ($this->instance === null) {
            $this->instance = parent::get($arguments, $dim);
        }
        return $this->instance;
    }
} 