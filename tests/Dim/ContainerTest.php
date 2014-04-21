<?php
/**
 * Dim - the PHP dependency injection manager.
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 *
 * @author    Dmitry Gres <dm.gres@gmail.com>
 * @copyright 2014 Dmitry Gres
 * @link      https://github.com/GR3S/Dim
 * @license   https://github.com/GR3S/Dim/blob/master/LICENSE MIT license
 * @version   1.0.0
 * @package   Dim
 */

namespace DimTest;

use ArrayObject;
use Dim\Container;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use stdClass;

/**
 * @coversDefaultClass Dim\Container
 */
class ContainerTest extends PHPUnit_Framework_TestCase
{
    protected $dim;

    public function setUp()
    {
        $this->dim = new Container;
    }

    public function tearDown()
    {
        $this->dim = null;
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $dim = $this->getMock('Dim\Container');
        $dim->expects($this->once())->method('set')->with($this->identicalTo($dim), $this->equalTo(get_class($dim)));
        $dim->__construct();
    }

    /**
     * @covers ::raw
     */
    public function testRaw()
    {
        $this->assertSame($this->dim, $this->dim->raw('Dim\Container'));
    }

    /**
     * @covers ::raw
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testRawException()
    {
        $this->dim->raw('foo');
    }

    /**
     * @covers ::set
     */
    public function testSetWithoutNames()
    {
        $service = $this->getMockBuilder('Dim\ServiceInterface')->disableOriginalConstructor()->getMock();
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
        $this->assertInstanceOf('Dim\ServiceInterface', $std);
    }

    /**
     * @covers ::set
     */
    public function testSetWithName()
    {
        $this->dim->set(new stdClass, 'std');
        $this->assertInstanceOf('stdClass', $this->dim->raw('std'));
    }

    /**
     * @covers ::set
     */
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
     * @covers ::set
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage A name of the dependency does not specified.
     */
    public function testSetException()
    {
        $this->dim->set('foo');
    }

    /**
     * @covers ::alias
     */
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

    /**
     * @covers ::alias
     */
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
     * @covers ::alias
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testAliasException()
    {
        $this->dim->alias('foo', 'bar');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $args = array(1, 2, 3);
        $service = $this->getMockBuilder('Dim\ServiceInterface')->disableOriginalConstructor()->getMock();
        $service->expects($this->once())->method('get')->with(
            $this->identicalTo($args),
            $this->identicalTo($this->dim)
        )->will($this->returnValue(new stdClass));
        $this->dim->set(new stdClass, 'std');
        $this->dim->set($service, 'svc');
        $this->assertSame($this->dim, $this->dim->get('Dim\Container', $args));
        $this->assertInstanceOf('stdClass', $this->dim->get('std', $args));
        $this->assertInstanceOf('stdClass', $this->dim->get('svc', $args));
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $this->dim->set(new stdClass);
        $this->assertTrue($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->has('Foo'));
    }

    /**
     * @covers ::remove
     */
    public function testRemove()
    {
        $this->dim->set(new stdClass);
        $this->dim->remove('stdClass');
        $this->assertFalse($this->dim->has('stdClass'));
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $this->dim->set(new stdClass);
        $this->dim->set(new ArrayObject);
        $this->dim->clear();
        $this->assertFalse($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->has('ArrayObject'));
        $this->assertFalse($this->dim->has('ArrayAccess'));
        $this->assertFalse($this->dim->has('Countable'));
        $this->assertTrue($this->dim->has('Dim\Container'));
    }

    /**
     * @covers ::getNames
     */
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

    /**
     * @covers ::offsetExists
     */
    public function testOffsetExists()
    {
        $dim = $this->getMock('Dim\Container', array('has'));
        $dim->expects($this->once())->method('has')->with($this->equalTo('foo'));
        isset($dim['foo']);
    }

    /**
     * @covers ::offsetSet
     */
    public function testOffsetSet()
    {
        $dim = $this->getMock('Dim\Container', array('set'));
        $dim->expects($this->once())->method('set')->with($this->equalTo('bar'), $this->equalTo('foo'));
        $dim['foo'] = 'bar';
    }

    /**
     * @covers ::offsetGet
     */
    public function testOffsetGet()
    {
        $dim = $this->getMock('Dim\Container', array('get'));
        $dim->expects($this->once())->method('get')->with($this->equalTo('foo'));
        $dim['foo'];
    }

    /**
     * @covers ::offsetUnset
     */
    public function testOffsetUnset()
    {
        $dim = $this->getMock('Dim\Container', array('remove'));
        $dim->expects($this->once())->method('remove')->with($this->equalTo('foo'));
        unset($dim['foo']);
    }

    /**
     * @covers ::__isset
     */
    public function testIsset()
    {
        $dim = $this->getMock('Dim\Container', array('has'));
        $dim->expects($this->once())->method('has')->with($this->equalTo('foo'));
        isset($dim->foo);
    }

    /**
     * @covers ::__set
     */
    public function testMagicSet()
    {
        $dim = $this->getMock('Dim\Container', array('set'));
        $dim->expects($this->once())->method('set')->with($this->equalTo('bar'), $this->equalTo('foo'));
        $dim->foo = 'bar';
    }

    /**
     * @covers ::__get
     */
    public function testMagicGet()
    {
        $dim = $this->getMock('Dim\Container', array('get'));
        $dim->expects($this->once())->method('get')->with($this->equalTo('foo'));
        $dim->foo;
    }

    /**
     * @covers ::__unset
     */
    public function testUnset()
    {
        $dim = $this->getMock('Dim\Container', array('remove'));
        $dim->expects($this->once())->method('remove')->with($this->equalTo('foo'));
        unset($dim->foo);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $dim = $this->getMock('Dim\Container', array('get'));
        $dim->expects($this->once())->method('get')->with($this->equalTo('foo'), $this->equalTo('bar'));
        $dim('foo', 'bar');
    }

    /**
     * @covers ::scope
     * @covers ::getScope
     */
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

    /**
     * @covers ::scope
     * @covers ::getScope
     */
    public function testSubScope()
    {
        $this->dim->scope('foo')->scope('bar')->set(new stdClass);
        $this->assertFalse($this->dim->has('stdClass'));
        $this->assertFalse($this->dim->scope('foo')->has('stdClass'));
        $this->assertFalse($this->dim->scope('bar')->has('stdClass'));
        $this->assertTrue($this->dim->scope('foo')->scope('bar')->has('stdClass'));
    }

    /**
     * @covers ::scope
     * @covers ::getScope
     */
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
     * @covers ::scope
     * @covers ::getScope
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testScopeException()
    {
        $this->dim->scope('foo', 'bar');
    }

    /**
     * @covers ::scope
     * @covers ::getScope
     * @covers ::raw
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeRawException()
    {
        $this->dim->set(new stdClass);
        $this->dim->scope('foo')->raw('stdClass');
    }

    /**
     * @covers ::scope
     * @covers ::getScope
     * @covers ::alias
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeAliasException()
    {
        $this->dim->set(new stdClass);
        $this->dim->scope('foo')->alias('stdClass', 'foo');
    }
}
