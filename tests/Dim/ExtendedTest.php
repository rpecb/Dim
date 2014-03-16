<?php

class ExtendedTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $extended = new Extended($this->getMock('Dim'), 'stdClass', function ($value) {
            return new $value;
        });
        $this->assertInstanceOf('stdClass', $extended->get());
    }
}
 