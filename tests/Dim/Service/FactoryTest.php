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

use Dim\Service\Factory;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @coversDefaultClass Dim\Service\Factory
 * @covers Dim\Service\Factory
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testConstructException()
    {
        new Factory('stdClass', 'foo');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $args1 = array(1, 2, 3);
        $args2 = array(3 => 4, 5, 6);
        $callable = function () {
            return new stdClass;
        };
        $dim = $this->getMock('Dim\Container');
        $service =
            $this->getMockBuilder('Dim\Service\Factory')->setMethods(array('resolveCallable'))->setConstructorArgs(
                array('stdClass', $callable, $args2)
            )->getMock();
        $service->staticExpects($this->once())->method('resolveCallable')->with(
            $this->identicalTo($callable),
            $this->identicalTo($args1 + $args2),
            $this->identicalTo($dim)
        )->will($this->returnValue($callable()));
        $this->assertInstanceOf('stdClass', $service->get($args1, $dim));
    }
}
 