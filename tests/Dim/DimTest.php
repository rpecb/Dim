<?php

class DimTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct() # Dim Mock Without constructor (set)
    {
        $dim = $this->getMockBuilder('Dim')->disableOriginalConstructor()->setMethods(array('set'))->getMock();
        $dim->expects($this->once())->method('set')->with($this->identicalTo($dim), $this->stringContains('Dim'));
        $dim->__construct();
    }

    public function testRaw() # Dim
    {
        $dim = new Dim;
        $this->assertSame($dim, $dim->raw('Dim'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testRawException() # Dim
    {
        $dim = new Dim;
        $dim->raw('foo');
    }

    public function testSetWithoutNames() # Dim, Service Mock without constructor (getClass)
    {
        $dim = new Dim;
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('getClass')->will($this->returnValue('stdClass'));
        $dim->set(new ArrayObject);
        $dim->set($service);
        $foo = $dim->raw('ArrayObject');
        $bar = $dim->raw('ArrayAccess');
        $foobar = $dim->raw('Countable');
        $std = $dim->raw('stdClass');
        $this->assertInstanceOf('ArrayObject', $foo);
        $this->assertInstanceOf('ArrayObject', $bar);
        $this->assertInstanceOf('ArrayObject', $foobar);
        $this->assertSame($foo, $bar);
        $this->assertInstanceOf('Service', $std);
    }

    public function testSetWithName() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass, 'std');
        $this->assertInstanceOf('stdClass', $dim->raw('std'));
    }

    public function testSetWithNames() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass, array('foo', 'bar'));
        $foo = $dim->raw('foo');
        $bar = $dim->raw('bar');
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Service name does not specified.
     */
    public function testSetException() # Dim
    {
        $dim = new Dim;
        $dim->set('foo');
    }

    public function testAlias() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->alias('stdClass', 'foo');
        $foo1 = $dim->raw('stdClass');
        $foo2 = $dim->raw('foo');
        $this->assertInstanceOf('stdClass', $foo1);
        $this->assertInstanceOf('stdClass', $foo2);
        $this->assertSame($foo1, $foo2);
    }

    public function testAliases() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->alias('stdClass', array('foo', 'bar'));
        $foo = $dim->raw('stdClass');
        $foo1 = $dim->raw('foo');
        $bar = $dim->raw('bar');
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $foo1);
        $this->assertInstanceOf('stdClass', $bar);
        $this->assertSame($foo, $bar);
        $this->assertSame($foo1, $bar);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testAliasException() # Dim
    {
        $dim = new Dim;
        $dim->alias('foo', 'bar');
    }

    public function testGet() # Dim, Service Mock without constructor (get)
    {
        $args = array(1, 2, 3);
        $dim = new Dim;
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('get')->with(
            $this->identicalTo($args),
            $this->identicalTo($dim)
        )->will($this->returnValue(new stdClass));
        $dim->set(new stdClass, 'std');
        $dim->set($service, 'svc');
        $this->assertSame($dim, $dim->get('Dim', $args));
        $this->assertInstanceOf('stdClass', $dim->get('std', $args));
        $this->assertInstanceOf('stdClass', $dim->get('svc', $args));
    }

    public function testHas() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $this->assertTrue($dim->has('stdClass'));
        $this->assertFalse($dim->has('Foo'));
    }

    public function testRemove() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->remove('stdClass');
        $this->assertFalse($dim->has('stdClass'));
    }

    public function testClear() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->set(new ArrayObject);
        $dim->clear();
        $this->assertFalse($dim->has('stdClass'));
        $this->assertFalse($dim->has('ArrayObject'));
        $this->assertFalse($dim->has('ArrayAccess'));
        $this->assertFalse($dim->has('Countable'));
        $this->assertTrue($dim->has('Dim'));
    }

    public function testGetNames() # Dim
    {
        $dim = new Dim;
        $reflection = new ReflectionClass($dim);
        $getNames = $reflection->getMethod('getNames');
        $getNames->setAccessible(true);
        $names = $getNames->invoke($dim, 'ArrayObject');
        $this->assertCount(6, $names);
        $this->assertContains('ArrayObject', $names);
        $this->assertContains('IteratorAggregate', $names);
        $this->assertContains('Traversable', $names);
        $this->assertContains('ArrayAccess', $names);
        $this->assertContains('Serializable', $names);
        $this->assertContains('Countable', $names);
    }

    public function testOffsetExists() # Dim mock (has)
    {
        $dim = $this->getMock('Dim', array('has'));
        $dim->expects($this->once())->method('has')->with($this->stringContains('foo'));
        isset($dim['foo']);
    }

    public function testOffsetSet() # Dim mock (set)
    {
        $dim = $this->getMock('Dim', array('set'));
        $dim->expects($this->once())->method('set')->with($this->stringContains('bar'), $this->stringContains('foo'));
        $dim['foo'] = 'bar';
    }

    public function testOffsetGet() # Dim mock (get)
    {
        $dim = $this->getMock('Dim', array('get'));
        $dim->expects($this->once())->method('get')->with($this->stringContains('foo'));
        $dim['foo'];
    }

    public function testOffsetUnset() # Dim mock (remove)
    {
        $dim = $this->getMock('Dim', array('remove'));
        $dim->expects($this->once())->method('remove')->with($this->stringContains('foo'));
        unset($dim['foo']);
    }

    public function testIsset() # Dim mock (has)
    {
        $dim = $this->getMock('Dim', array('has'));
        $dim->expects($this->once())->method('has')->with($this->stringContains('foo'));
        isset($dim->foo);
    }

    public function testMagicSet() # Dim mock (set)
    {
        $dim = $this->getMock('Dim', array('set'));
        $dim->expects($this->once())->method('set')->with($this->stringContains('bar'), $this->stringContains('foo'));
        $dim->foo = 'bar';
    }

    public function testMagicGet() # Dim mock (get)
    {
        $dim = $this->getMock('Dim', array('get'));
        $dim->expects($this->once())->method('get')->with($this->stringContains('foo'));
        $dim->foo;
    }

    public function testUnset() # Dim mock (remove)
    {
        $dim = $this->getMock('Dim', array('remove'));
        $dim->expects($this->once())->method('remove')->with($this->stringContains('foo'));
        unset($dim->foo);
    }

    public function testInvoke() # Dim mock (get)
    {
        $dim = $this->getMock('Dim', array('get'));
        $dim->expects($this->once())->method('get')->with($this->stringContains('foo'), $this->stringContains('bar'));
        $dim('foo', 'bar');
    }

    public function testScope() # Dim
    {
        $dim = new Dim;
        $dim->scope('foo')->set(new stdClass);
        $dim->scope('foo')->alias('stdClass', 'std');

        $this->assertFalse($dim->has('stdClass'));
        $this->assertFalse($dim->has('std'));
        $this->assertTrue($dim->scope('foo')->has('stdClass'));
        $this->assertTrue($dim->scope('foo')->has('std'));

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('stdClass'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('std'));
        $this->assertSame($dim->scope('foo')->raw('stdClass'), $dim->scope('foo')->raw('std'));

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('stdClass'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('std'));
        $this->assertSame($dim->scope('foo')->get('stdClass'), $dim->scope('foo')->get('std'));

        $dim->scope('foo')->remove('stdClass');
        $this->assertFalse($dim->scope('foo')->has('stdClass'));

        $dim->scope('foo')->clear();
        $this->assertFalse($dim->scope('foo')->has('std'));
    }

    public function testSubScope() # Dim
    {
        $dim = new Dim;
        $dim->scope('foo')->scope('bar')->set(new stdClass);
        $this->assertFalse($dim->has('stdClass'));
        $this->assertFalse($dim->scope('foo')->has('stdClass'));
        $this->assertFalse($dim->scope('bar')->has('stdClass'));
        $this->assertTrue($dim->scope('foo')->scope('bar')->has('stdClass'));
    }

    public function testScopeWithCallable() # Dim
    {
        $dim = new Dim;
        $dim->scope(
            'foo',
            function () use ($dim) {
                $dim->set('stdClass', 'std1');
                $dim->set('stdClass', 'std2');
            }
        );
        $this->assertFalse($dim->has('std1'));
        $this->assertFalse($dim->has('std2'));
        $this->assertTrue($dim->scope('foo')->has('std1'));
        $this->assertTrue($dim->scope('foo')->has('std2'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testScopeException() # Dim
    {
        $dim = new Dim;
        $dim->scope('foo', 'bar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeRawException() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->scope('foo')->raw('stdClass');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeAliasException() # Dim
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->scope('foo')->alias('stdClass', 'foo');
    }
}
 