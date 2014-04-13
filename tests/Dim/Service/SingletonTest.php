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

namespace DimTest\Service;

use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @coversDefaultClass Dim\Service\Singleton
 * @covers Dim\Service\Singleton
 */
class SingletonTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::get
     */
    public function testGet()
    {
        $args1 = array(1, 2, 3);
        $args2 = array(3 => 4, 5, 6);
        $dim = $this->getMock('Dim\Container');
        $service =
            $this->getMockBuilder('Dim\Service\Singleton')->setMethods(array('resolveClass'))->setConstructorArgs(
                array('stdClass', $args2)
            )->getMock();
        $service->staticExpects($this->once())->method('resolveClass')->with(
            $this->equalTo('stdClass'),
            $this->identicalTo($args1 + $args2),
            $this->identicalTo($dim)
        )->will($this->returnValue(new stdClass));
        $foo = $service->get($args1, $dim);
        $bar = $service->get($args1, $dim);
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar);
        $this->assertSame($foo, $bar);
    }
}