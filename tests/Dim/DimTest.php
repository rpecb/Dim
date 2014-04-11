<?php

class DimTest extends PHPUnit_Framework_TestCase
{
    protected $dim;

    public function setUp()
    {
        $this->dim = new Dim;
    }

    public function tearDown()
    {
        $this->dim = null;
    }

    public function testConstruct()
    {
        $dim = $this->getMock('Dim');
        $dim->expects($this->once())->method('set')->with($this->identicalTo($dim), $this->stringContains('Dim'));
        $dim->__construct();
    }

    public function testRaw()
    {
        $this->assertSame($this->dim, $this->dim->raw('Dim'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testRawException()
    {
        $this->dim->raw('foo');
    }

    public function testSetWithoutNames()
    {
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('getClass')->will($this->returnValue('stdClass'));
        $this->dim->set(new ArrayObject);
        $this->dim->set($service);
        $foo = $this->dim->raw('ArrayObject');
        $bar = $this->dim->raw('ArrayAccess');
        $foobar = $this->dim->raw('Countable');
        $std = $this->dim->raw('stdClass');
        $this->assertInstanceOf('ArrayObject', $foo);
        $this->assertInstanceOf('ArrayObject', $bar);
        $this->assertInstanceOf('ArrayObject', $foobar);
        $this->assertSame($foo, $bar);
        $this->assertInstanceOf('Service', $std);
    }

    public function testSetWithName()
    {
        $this->dim->set(new stdClass, 'std');
        $this->assertInstanceOf('stdClass', $this->dim->raw('std'));
    }

    public function testSetWithNames()
    {
        $this->dim->set(new stdClass, array('foo', 'bar'));
        $foo = $this->dim->raw('foo');
        $bar = $this->dim->raw('bar');
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Service name does not specified.
     */
    public function testSetException()
    {
        $this->dim->set('foo');
    }

    public function testAlias()
    {
        $this->dim->set(new stdClass);
        $this->dim->alias('stdClass', 'foo');
        $foo1 = $this->dim->raw('stdClass');
        $foo2 = $this->dim->raw('foo');
        $this->assertInstanceOf('stdClass', $foo1);
        $this->assertInstanceOf('stdClass', $foo2);
        $this->assertSame($foo1, $foo2);
    }

    public function testAliases()
    {
        $this->dim->set(new stdClass);
        $this->dim->alias('stdClass', array('foo', 'bar'));
        $foo = $this->dim->raw('stdClass');
        $foo1 = $this->dim->raw('foo');
        $bar = $this->dim->raw('bar');
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
    public function testAliasException()
    {
        $this->dim->alias('foo', 'bar');
    }

    public function testGet()
    {
        $args = array(1, 2, 3);
        $service = $this->getMockBuilder('Service')->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('get')->with(
            $this->identicalTo($args),
            $this->identicalTo($this->dim)
        )->will($this->returnValue(new stdClass));
        $this->dim->set(new stdClass, 'std');
        $this->dim->set($service, 'svc');
        $this->assertSame($this->dim, $this->dim->get('Dim', $args));
        $this->assertInstanceOf('stdClass', $this->dim->get('std', $args));
        $this->assertInstanceOf('stdClass', $this->dim->get('svc', $args));
    }

    public function testHas()
    {
        $this->dim->set(new stdClass);
        $this->assertTrue($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->has('Foo'));
    }

    public function testRemove()
    {
        $this->dim->set(new stdClass);
        $this->dim->remove('stdClass');
        $this->assertFalse($this->dim->has('stdClass'));
    }

    public function testClear()
    {
        $this->dim->set(new stdClass);
        $this->dim->set(new ArrayObject);
        $this->dim->clear();
        $this->assertFalse($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->has('ArrayObject'));
        $this->assertFalse($this->dim->has('ArrayAccess'));
        $this->assertFalse($this->dim->has('Countable'));
        $this->assertTrue($this->dim->has('Dim'));
    }

    public function testGetNames()
    {
        $reflection = new ReflectionClass($this->dim);
        $getNames = $reflection->getMethod('getNames');
        $getNames->setAccessible(true);
        $names = $getNames->invoke($this->dim, 'ArrayObject');
        $this->assertCount(6, $names);
        $this->assertContains('ArrayObject', $names);
        $this->assertContains('IteratorAggregate', $names);
        $this->assertContains('Traversable', $names);
        $this->assertContains('ArrayAccess', $names);
        $this->assertContains('Serializable', $names);
        $this->assertContains('Countable', $names);
    }

    public function testOffsetExists()
    {
        $dim = $this->getMock('Dim', array('has'));
        $dim->expects($this->once())->method('has')->with($this->stringContains('foo'));
        isset($dim['foo']);
    }

    public function testOffsetSet()
    {
        $dim = $this->getMock('Dim', array('set'));
        $dim->expects($this->once())->method('set')->with($this->stringContains('bar'), $this->stringContains('foo'));
        $dim['foo'] = 'bar';
    }

    public function testOffsetGet()
    {
        $dim = $this->getMock('Dim', array('get'));
        $dim->expects($this->once())->method('get')->with($this->stringContains('foo'));
        $dim['foo'];
    }

    public function testOffsetUnset()
    {
        $dim = $this->getMock('Dim', array('remove'));
        $dim->expects($this->once())->method('remove')->with($this->stringContains('foo'));
        unset($dim['foo']);
    }

    public function testIsset()
    {
        $dim = $this->getMock('Dim', array('has'));
        $dim->expects($this->once())->method('has')->with($this->stringContains('foo'));
        isset($dim->foo);
    }

    public function testMagicSet()
    {
        $dim = $this->getMock('Dim', array('set'));
        $dim->expects($this->once())->method('set')->with($this->stringContains('bar'), $this->stringContains('foo'));
        $dim->foo = 'bar';
    }

    public function testMagicGet()
    {
        $dim = $this->getMock('Dim', array('get'));
        $dim->expects($this->once())->method('get')->with($this->stringContains('foo'));
        $dim->foo;
    }

    public function testUnset()
    {
        $dim = $this->getMock('Dim', array('remove'));
        $dim->expects($this->once())->method('remove')->with($this->stringContains('foo'));
        unset($dim->foo);
    }

    public function testInvoke()
    {
        $dim = $this->getMock('Dim', array('get'));
        $dim->expects($this->once())->method('get')->with($this->stringContains('foo'), $this->stringContains('bar'));
        $dim('foo', 'bar');
    }

    public function testScope()
    {
        $this->dim->scope('foo')->set(new stdClass);
        $this->dim->scope('foo')->alias('stdClass', 'std');

        $this->assertFalse($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->has('std'));
        $this->assertTrue($this->dim->scope('foo')->has('stdClass'));
        $this->assertTrue($this->dim->scope('foo')->has('std'));

        $this->assertInstanceOf('stdClass', $this->dim->scope('foo')->raw('stdClass'));
        $this->assertInstanceOf('stdClass', $this->dim->scope('foo')->raw('std'));
        $this->assertSame($this->dim->scope('foo')->raw('stdClass'), $this->dim->scope('foo')->raw('std'));

        $this->assertInstanceOf('stdClass', $this->dim->scope('foo')->get('stdClass'));
        $this->assertInstanceOf('stdClass', $this->dim->scope('foo')->get('std'));
        $this->assertSame($this->dim->scope('foo')->get('stdClass'), $this->dim->scope('foo')->get('std'));

        $this->dim->scope('foo')->remove('stdClass');
        $this->assertFalse($this->dim->scope('foo')->has('stdClass'));

        $this->dim->scope('foo')->clear();
        $this->assertFalse($this->dim->scope('foo')->has('std'));
    }

    public function testSubScope()
    {
        $this->dim->scope('foo')->scope('bar')->set(new stdClass);
        $this->assertFalse($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->scope('foo')->has('stdClass'));
        $this->assertFalse($this->dim->scope('bar')->has('stdClass'));
        $this->assertTrue($this->dim->scope('foo')->scope('bar')->has('stdClass'));
    }

    public function testScopeWithCallable()
    {
        $dim = $this->dim;
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
    public function testScopeException()
    {
        $this->dim->scope('foo', 'bar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeRawException()
    {
        $this->dim->set(new stdClass);
        $this->dim->scope('foo')->raw('stdClass');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeAliasException()
    {
        $this->dim->set(new stdClass);
        $this->dim->scope('foo')->alias('stdClass', 'foo');
    }
}
 