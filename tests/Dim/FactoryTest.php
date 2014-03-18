<?php

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testConstructException()
    {
        new Factory('foo');
    }

    public function testGet()
    {
        $factory = new Factory(function () {
            return new stdClass;
        });
        $this->assertInstanceOf('stdClass', $factory->get());
    }
}
 