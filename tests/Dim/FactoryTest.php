<?php

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testConstructException()
    {
        new Factory('stdClass', 'foo');
    }

    public function testGet()
    {
        $factory = new Factory('stdClass', function () {
            return new stdClass;
        });
        $this->assertInstanceOf('stdClass', $factory->get());
    }
}
 