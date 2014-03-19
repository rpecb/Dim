<?php

class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A class name expected.
     */
    public function testConstructException()
    {
        new Service('FooBar');
    }

    public function testGetReflectionParameters()
    {
        $foo = new stdClass;
        $dim = $this->getMock('Dim');
        $dim->expects($this->once())
            ->method('has')
            ->with($this->stringContains('stdClass'))
            ->will($this->returnValue(true));
        $dim->expects($this->once())
            ->method('get')
            ->with($this->stringContains('stdClass'))
            ->will($this->returnValue($foo));
        $class = new ReflectionClass('Service');
        $getReflectionParameters = $class->getMethod('getReflectionParameters');
        $getReflectionParameters->setAccessible(true);
        $reflection = new ReflectionFunction(function (stdClass $foo, $bar, $foobar, $null = null) {
        });
        $parameters = $getReflectionParameters->invokeArgs(
            new Service('stdClass'),
            array($reflection, array('bar' => 'bar', 2 => 'foobar'), $dim)
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
        $service = new Service('stdClass');
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
        $service = new Service('stdClass');
        $this->getMockBuilder('stdClass')->setMockClassName('stdClass1')->setMethods(array('__construct'))->getMock();
        $this->assertInstanceOf('stdClass', $resolveClass->invoke($service, 'stdClass'));
        $this->assertInstanceOf('stdClass1', $resolveClass->invoke($service, 'stdClass1'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Bar class is not instantiable.
     */
    public function testResolveClassException()
    {
        $class = new ReflectionClass('Service');
        $resolveClass = $class->getMethod('resolveClass');
        $resolveClass->setAccessible(true);
        $service = new Service('Bar');
        $resolveClass->invoke($service, 'Bar');
    }

    /**
     * @depends testGetReflectionParameters
     */
    public function testResolveCallable()
    {
        $class = new ReflectionClass('Service');
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $service = new Service('Foo');
        $this->assertInstanceOf('Foo', $resolveCallable->invoke($service, array(new Foo, 'factory')));
        $this->assertInstanceOf('Foo', $resolveCallable->invoke($service, 'Foo::factory'));
        $this->assertInstanceOf('Foo', $resolveCallable->invoke($service, new Foo));
        $this->assertInstanceOf(
            'Foo',
            $resolveCallable->invoke(
                $service,
                function () {
                    return new Foo;
                }
            )
        );
        function foobar()
        {
            return new Foo;
        }

        ;
        $this->assertInstanceOf('Foo', $resolveCallable->invoke($service, 'foobar'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Can not access to non-public method Foo::bar.
     */
    public function testResolveCallableException1()
    {
        $class = new ReflectionClass('Service');
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $service = new Service('Foo');
        $resolveCallable->invoke($service, array(new Foo, 'bar'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Can not access to non-public method Foo::bar.
     */
    public function testResolveCallableException2()
    {
        $class = new ReflectionClass('Service');
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $service = new Service('Foo');
        $resolveCallable->invoke($service, 'Foo::bar');
    }

    /**
     * @depends testResolveClass
     */
    public function testGet()
    {
        $service = new Service('stdClass');
        $this->assertInstanceOf('stdClass', $service->get());
    }

    /**
     * @depends testResolveClass
     * @depends testGet
     */
    public function testInvoke()
    {
        $service = new Service('stdClass');
        $this->assertInstanceOf('stdClass', $service());
    }
}
 