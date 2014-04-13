<?php

namespace DimTest\Service;

use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @coversDefaultClass Dim\Service\Extension
 * @covers Dim\Service\Extension
 */
class ExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::get
     */
    public function testGet()
    {
        $service = $this->getMockBuilder('Dim\Service')->disableOriginalConstructor()->getMock();
        $service->expects($this->at(0))->method('getClass')->will($this->returnValue('stdClass'));
        $service->expects($this->at(1))->method('get')->will($this->returnValue(new stdClass));
        $args1 = array(1, 2, 3);
        $args2 = array(3 => 4, 5, 6);
        $callable = function () {
            return new stdClass;
        };
        $dim = $this->getMock('Dim\Container');
        $extended =
            $this->getMockBuilder('Dim\Service\Extension')->setMethods(array('resolveCallable'))->setConstructorArgs(
                array($service, $callable, $args2)
            )->getMock();
        $extended->staticExpects($this->once())->method('resolveCallable')->with(
            $this->identicalTo($callable),
            $this->identicalTo($args1 + $args2),
            $this->identicalTo($dim)
        )->will($this->returnValue($callable()));
        $this->assertInstanceOf('stdClass', $extended->get($args1, $dim));
    }
}
 