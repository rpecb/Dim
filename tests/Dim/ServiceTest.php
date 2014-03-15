<?php

class ServiceTest extends PHPUnit_Framework_TestCase
{
    // TODO: with scopes, arguments, parent constructors, parent methods

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A class name expected.
     */
    public function testConstructException()
    {
        new Service($this->getMock('Dim'), 'Bar');
    }

    public function testGetReflectionParameters()
    {
        $foo = new Foo;
        $dim = $this->getMock('Dim');
        $dim->expects($this->once())
            ->method('get')
            ->with($this->stringContains('Foo'))
            ->will($this->returnValue($foo));
        $class = new ReflectionClass('Service');
        $getReflectionParameters = $class->getMethod('getReflectionParameters');
        $getReflectionParameters->setAccessible(true);
        $reflection = new ReflectionFunction(function (Foo $foo, $bar, $foobar, $null = null) {
        });
        $parameters = $getReflectionParameters->invokeArgs(
            new Service($dim, 'Foo'),
            array($reflection, array('bar' => 'bar', 2 => 'foobar'))
        );
        $this->assertCount(4, $parameters);
        $this->assertArrayHasKey(0, $parameters);
        $this->assertArrayHasKey(1, $parameters);
        $this->assertArrayHasKey(2, $parameters);
        $this->assertArrayHasKey(3, $parameters);
        $this->assertEquals($foo, $parameters[0]);
        $this->assertEquals('bar', $parameters[1]);
        $this->assertEquals('foobar', $parameters[2]);
        $this->assertNull($parameters[3]);
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testGetReflectionParametersException()
    {
        $service = new Service($this->getMock('Dim'), 'Foo');
        $class = new ReflectionClass('Service');
        $getReflectionParameters = $class->getMethod('getReflectionParameters');
        $getReflectionParameters->setAccessible(true);
        $reflection = new ReflectionFunction(function ($foo) {
        });
        $getReflectionParameters->invokeArgs($service, array($reflection));
    }

    /**
     * @depends testGetReflectionParameters
     */
    public function testResolveClass()
    {
        $class = new ReflectionClass('Service');
        $resolveClass = $class->getMethod('resolveClass');
        $resolveClass->setAccessible(true);
        $service = new Service($this->getMock('Dim'), 'Foo');
        $this->getMockBuilder('Foo')->setMockClassName('Foo1')->setMethods(array('__construct'))->getMock();
        $this->assertInstanceOf('Foo', $resolveClass->invoke($service, 'Foo'));
        $this->assertInstanceOf('Foo1', $resolveClass->invoke($service, 'Foo1'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Poo class is not instantiable.
     */
    public function testResolveClassException()
    {
        $class = new ReflectionClass('Service');
        $resolveClass = $class->getMethod('resolveClass');
        $resolveClass->setAccessible(true);
        $service = new Service($this->getMock('Dim'), 'Poo');
        $resolveClass->invoke($service, 'Poo');
    }

    /**
     * @depends testResolveClass
     */
    public function testGet()
    {
    }

    public function testInvoke()
    {
    }

    public function testResolveCallable()
    {
    }

    public function testResolveCallableException()
    {
    }
}
 