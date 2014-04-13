<?php

/**
 * @coversDefaultClass Service
 * @covers Service
 */
class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A class name expected.
     */
    public function testConstructException()
    {
        new Service('BarFoo');
    }

    /**
     * @covers ::getClass
     */
    public function testGetClass()
    {
        $service = new Service('stdClass');
        $this->assertEquals('stdClass', $service->getClass());
    }

    /**
     * @covers ::getReflectionParameters
     */
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
        $parameters = $getReflectionParameters->invoke(
            new Service('stdClass'),
            $reflection,
            array('bar' => 'bar', 2 => 'foobar'),
            $dim
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
     * @covers ::getReflectionParameters
     */
    public function testGetReflectionParametersWithScope()
    {
        $dim = $this->getMock('Dim');
        $std = new stdClass;
        $service = new Service('FooBarStub');
        $dim->expects($this->at(0))
            ->method('scope')
            ->with($this->stringContains('foo'))
            ->will($this->returnValue($dim));
        $dim->expects($this->at(1))
            ->method('set')
            ->with($this->isInstanceOf('stdClass'));
        $dim->scope('foo')->set($std);

        $dim->expects($this->at(0))
            ->method('scope')
            ->with($this->stringContains('foo'))
            ->will($this->returnValue($dim));
        $dim->expects($this->at(1))
            ->method('set')
            ->with($this->isInstanceOf('Service'));
        $dim->scope('foo')->set($service);

        $dim->expects($this->at(0))
            ->method('scope')
            ->with($this->stringContains('foo'))
            ->will($this->returnValue($dim));
        $dim->expects($this->at(2))
            ->method('has')
            ->with($this->stringContains('stdClass'))
            ->will($this->returnValue(true));
        $dim->expects($this->at(3))
            ->method('get')
            ->with($this->stringContains('stdClass'))
            ->will($this->returnValue($std));
        $dim->expects($this->at(1))
            ->method('get')
            ->with($this->stringContains('FooBarStub'))
            ->will(
                $this->returnCallback(
                    function () use ($service, $dim) {
                        $service->get(array('bar' => 'bar', 2 => 'foobar'), $dim);
                    }
                )
            );
        $dim->scope('foo')->get('FooBarStub');
    }

    /**
     * @covers ::getReflectionParameters
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
        $getReflectionParameters->invoke($service, $reflection);
    }

    /**
     * @covers ::resolveClass
     */
    public function testResolveClass()
    {
        $class = new ReflectionClass('Service');
        $resolveClass = $class->getMethod('resolveClass');
        $resolveClass->setAccessible(true);
        $service = new Service('stdClass');
        $this->assertInstanceOf('stdClass', $resolveClass->invoke($service, 'stdClass'));
    }

    /**
     * @covers ::resolveClass
     */
    public function testResolveClassWithArguments()
    {
        $arguments = array(new stdClass, 2, 3);
        $dim = $this->getMock('Dim');
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->setMethods(
            array('getReflectionParameters')
        )->getMock();
        $service->staticExpects($this->once())->method('getReflectionParameters')->with(
            $this->anything(),
            $this->identicalTo($arguments),
            $this->identicalTo($dim)
        )->will($this->returnValue($arguments));
        $class = new ReflectionClass($service);
        $resolveClass = $class->getMethod('resolveClass');
        $resolveClass->setAccessible(true);
        $this->assertInstanceOf('FooBarStub', $resolveClass->invoke($service, 'FooBarStub', $arguments, $dim));
    }

    /**
     * @covers ::resolveClass
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage BarStub class is not instantiable.
     */
    public function testResolveClassException()
    {
        $class = new ReflectionClass('Service');
        $resolveClass = $class->getMethod('resolveClass');
        $resolveClass->setAccessible(true);
        $service = new Service('BarStub');
        $resolveClass->invoke($service, 'BarStub');
    }

    /**
     * @covers ::resolveCallable
     */
    public function testResolveCallable()
    {
        $class = new ReflectionClass('Service');
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $service = new Service('FooStub');
        $this->assertInstanceOf('FooStub', $resolveCallable->invoke($service, array(new FooStub, 'factory')));
        $this->assertInstanceOf('FooStub', $resolveCallable->invoke($service, 'FooStub::factory'));
        $this->assertInstanceOf('FooStub', $resolveCallable->invoke($service, new FooStub));
        $this->assertInstanceOf(
            'stdClass',
            $resolveCallable->invoke(
                $service,
                function () {
                    return new stdClass;
                }
            )
        );
        function foobar()
        {
            return new stdClass;
        }

        ;
        $this->assertInstanceOf('stdClass', $resolveCallable->invoke($service, 'foobar'));
    }

    /**
     * @covers ::resolveCallable
     */
    public function testResolveCallableWithArguments()
    {
        $args = array(1, 2, 3);
        $dim = $this->getMock('Dim');
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->setMethods(
            array('getReflectionParameters')
        )->getMock();
        $service->staticExpects($this->once())->method('getReflectionParameters')->with(
            $this->anything(),
            $this->identicalTo($args),
            $this->identicalTo($dim)
        )->will($this->returnValue($args));
        $class = new ReflectionClass($service);
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $this->assertInstanceOf(
            'stdClass',
            $resolveCallable->invoke(
                $service,
                function () {
                    return new stdClass;
                },
                $args,
                $dim
            )
        );
    }

    /**
     * @covers ::resolveCallable
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Can not access to non-public method FooStub::bar.
     */
    public function testResolveCallableException1()
    {
        $class = new ReflectionClass('Service');
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $service = new Service('FooStub');
        $resolveCallable->invoke($service, array(new FooStub, 'bar'));
    }

    /**
     * @covers ::resolveCallable
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Can not access to non-public method FooStub::bar.
     */
    public function testResolveCallableException2()
    {
        $class = new ReflectionClass('Service');
        $resolveCallable = $class->getMethod('resolveCallable');
        $resolveCallable->setAccessible(true);
        $service = new Service('FooStub');
        $resolveCallable->invoke($service, 'FooStub::bar');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $args1 = array(1, 2, 3);
        $args2 = array(3 => 4, 5, 6);
        $dim = $this->getMock('Dim');
        $service = $this->getMockBuilder('Service')->setMethods(array('resolveClass'))->setConstructorArgs(
            array('stdClass', $args2)
        )->getMock();
        $service->staticExpects($this->once())->method('resolveClass')->with(
            $this->stringContains('stdClass'),
            $this->identicalTo($args1 + $args2),
            $this->identicalTo($dim)
        )->will($this->returnValue(new stdClass));
        $this->assertInstanceOf('stdClass', $service->get($args1, $dim));
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $args = array(1, 2, 3);
        $dim = $this->getMock('Dim');
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->setMethods(array('get'))->getMock();
        $service->expects($this->once())->method('get')->with($this->identicalTo($args), $this->identicalTo($dim));
        $service($args, $dim);
    }
}
 