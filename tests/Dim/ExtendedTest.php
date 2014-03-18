<?php

class ExtendedTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $extended = new Extended('stdClass', function ($value) {
            return new $value;
        });
        $this->assertInstanceOf('stdClass', $extended->get());
    }
}
 