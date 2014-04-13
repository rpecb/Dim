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

namespace DimTest;

class FooStub
{
    public static function factory()
    {
        return new static;
    }

    protected static function bar()
    {
        return new static;
    }

    public function __invoke()
    {
        return static::factory();
    }
}