<?php

class ExtendedTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('getClass')->will($this->returnValue('stdClass'));
        $service->expects($this->once())->method('get')->will($this->returnValue(new stdClass));
        $extended = new Extension($service, function ($value) {
            return new $value;
        });
        $this->assertInstanceOf('stdClass', $extended->get());
    }
}
 