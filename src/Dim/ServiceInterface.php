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

namespace Dim;

/**
 * Interface implemented by service classes.
 *
 * @package Dim
 * @author  Dmitry Gres <dm.gres@gmail.com>
 * @license https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @link    https://github.com/GR3S/Dim/blob/master/src/Dim/ServiceInterface.php
 * @since   1.0.0
 */
interface ServiceInterface
{
    /**
     * @return mixed
     */
    public function getClass();

    /**
     * @return mixed
     */
    public function get($arguments = null, Container $dim = null);

    /**
     * @return mixed
     */
    public function __invoke($arguments = null, Container $dim = null);
} 