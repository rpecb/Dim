<?php

class DimTest extends PHPUnit_Framework_TestCase
{
    public function testConstructAndRaw()
    {
        $dim = new Dim;
        $this->assertSame($dim, $dim->raw('Dim'));
    }

    /**
     * @depends testConstructAndRaw
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testRawException()
    {
        $dim = new Dim;
        $dim->raw('foo');
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSetWithoutNames()
    {
        $dim = new Dim;
        $dim->set(new ArrayObject);
        $foo = $dim->raw('ArrayObject');
        $bar = $dim->raw('ArrayAccess');
        $foobar = $dim->raw('Countable');
        $this->assertInstanceOf('ArrayObject', $foo);
        $this->assertInstanceOf('ArrayObject', $bar);
        $this->assertInstanceOf('ArrayObject', $foobar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSetWithName()
    {
        $dim = new Dim;
        $dim->set(new stdClass, 'std');
        $this->assertInstanceOf('stdClass', $dim->raw('std'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSetWithNames()
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
     * @depends testConstructAndRaw
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Service name does not specified.
     */
    public function testSetException()
    {
        $dim = new Dim;
        $dim->set('foo');
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     */
    public function testAlias()
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

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     */
    public function testAliases()
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
     * @depends testConstructAndRaw
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testAliasException()
    {
        $dim = new Dim;
        $dim->alias('foo', 'bar');
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     */
    public function testHas()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $this->assertTrue($dim->has('stdClass'));
        $this->assertFalse($dim->has('Foo'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testHas
     */
    public function testRemove()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->remove('stdClass');
        $this->assertFalse($dim->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testHas
     */
    public function testClear()
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

    /**
     * @depends testConstructAndRaw
     */
    public function testGetNames()
    {
        $dim = new Dim;
        $reflection = new ReflectionClass('Dim');
        $getNames = $reflection->getMethod('getNames');
        $getNames->setAccessible(true);
        $names = $getNames->invokeArgs($dim, array('ArrayObject'));
        $this->assertCount(6, $names);
        $this->assertContains('ArrayObject', $names);
        $this->assertContains('IteratorAggregate', $names);
        $this->assertContains('Traversable', $names);
        $this->assertContains('ArrayAccess', $names);
        $this->assertContains('Serializable', $names);
        $this->assertContains('Countable', $names);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testHas
     */
    public function testOffsetExists()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $this->assertTrue(isset($dim['stdClass']));
        $this->assertFalse(isset($dim['Foo']));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     */
    public function testOffsetSet()
    {
        $dim = new Dim;
        $dim['std'] = new stdClass;
        $this->assertInstanceOf('stdClass', $dim->raw('std'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testRemove
     * @depends testHas
     */
    public function testOffsetUnset()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        unset($dim['stdClass']);
        $this->assertFalse($dim->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testHas
     */
    public function testIsset()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $this->assertTrue(isset($dim->stdClass));
        $this->assertFalse(isset($dim->Foo));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     */
    public function testMagicSet()
    {
        $dim = new Dim;
        $dim->std = new stdClass;
        $this->assertInstanceOf('stdClass', $dim->raw('std'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testRemove
     * @depends testHas
     */
    public function testUnset()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        unset($dim->stdClass);
        $this->assertFalse($dim->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     */
    public function testGet()
    {
        $dim = new Dim;
        $dim->set(new stdClass, 'std');
        $this->assertSame($dim, $dim->get('Dim'));
        $foo = $dim->get('std');
        $this->assertInstanceOf('stdClass', $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     * @depends testGet
     */
    public function testOffsetGet()
    {
        $dim = new Dim;
        $dim->set(new stdClass, 'std');
        $this->assertSame($dim, $dim['Dim']);
        $foo = $dim['std'];
        $this->assertInstanceOf('stdClass', $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     * @depends testGet
     */
    public function testInvoke()
    {
        $dim = new Dim;
        $dim->set(new stdClass, 'std');
        $this->assertSame($dim, $dim('Dim'));
        $foo = $dim('std');
        $this->assertInstanceOf('stdClass', $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     * @depends testGet
     */
    public function testMagicGet()
    {
        $dim = new Dim;
        $dim->set(new stdClass, 'std');
        $this->assertSame($dim, $dim->Dim);
        $foo = $dim->std;
        $this->assertInstanceOf('stdClass', $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithName
     * @depends testOffsetSet
     * @depends testMagicSet
     * @depends testAlias
     * @depends testHas
     * @depends testOffsetExists
     * @depends testIsset
     * @depends testGet
     * @depends testOffsetGet
     * @depends testMagicGet
     * @depends testInvoke
     * @depends testRemove
     * @depends testOffsetUnset
     * @depends testUnset
     * @depends testClear
     */
    public function testScope()
    {
        $dim = new Dim;
        $dim->scope('foo')->set(new stdClass, 'std1');
        $scope = $dim->scope('foo');
        $scope['std2'] = new stdClass;
        $dim->scope('foo')->std3 = new stdClass;
        $dim->scope('foo')->alias('std1', 'std');

        $this->assertFalse($dim->has('std'));
        $this->assertFalse($dim->has('std1'));
        $this->assertFalse($dim->has('std2'));
        $this->assertFalse($dim->has('std3'));
        $this->assertTrue($dim->scope('foo')->has('std'));
        $this->assertTrue($dim->scope('foo')->has('std1'));
        $this->assertTrue($dim->scope('foo')->has('std2'));
        $this->assertTrue($dim->scope('foo')->has('std2'));

        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['std']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['std1']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['std2']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['std3']));

        $this->assertTrue(isset($dim->scope('foo')->std));
        $this->assertTrue(isset($dim->scope('foo')->std1));
        $this->assertTrue(isset($dim->scope('foo')->std2));
        $this->assertTrue(isset($dim->scope('foo')->std3));

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('std'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('std1'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('std2'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('std3'));
        $this->assertSame($dim->scope('foo')->raw('std'), $dim->scope('foo')->raw('std1'));

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('std'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('std1'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('std2'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('std3'));
        $this->assertSame($dim->scope('foo')->get('std'), $dim->scope('foo')->get('std1'));

        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['std']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['std1']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['std2']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['std3']);
        $scope = $dim->scope('foo');
        $std = $scope['std'];
        $scope = $dim->scope('foo');
        $std1 = $scope['std1'];
        $this->assertSame($std, $std1);

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->std);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->std1);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->std2);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->std3);
        $std = $dim->scope('foo')->std;
        $std1 = $dim->scope('foo')->std1;
        $this->assertSame($std, $std1);

        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('std'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('std1'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('std2'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('std3'));
        $scope = $dim->scope('foo');
        $std = $scope('std');
        $scope = $dim->scope('foo');
        $std1 = $scope('std1');
        $this->assertSame($std, $std1);

        $dim->scope('foo')->remove('std');
        $scope = $dim->scope('foo');
        unset($scope['std1']);
        unset($dim->scope('foo')->std2);
        $this->assertFalse($dim->scope('foo')->has('std'));
        $this->assertFalse($dim->scope('foo')->has('std1'));
        $this->assertFalse($dim->scope('foo')->has('std2'));

        $dim->scope('foo')->clear();
        $this->assertFalse($dim->scope('foo')->has('std3'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testScope
     * @depends testSetWithoutNames
     * @depends testHas
     */
    public function testSubScope()
    {
        $dim = new Dim;
        $dim->scope('foo')->scope('bar')->set(new stdClass);
        $this->assertFalse($dim->has('stdClass'));
        $this->assertFalse($dim->scope('foo')->has('stdClass'));
        $this->assertFalse($dim->scope('bar')->has('stdClass'));
        $this->assertTrue($dim->scope('foo')->scope('bar')->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testScope
     * @depends testSetWithName
     * @depends testHas
     */
    public function testScopeWithCallable()
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
     * @depends testConstructAndRaw
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testScopeException()
    {
        $dim = new Dim;
        $dim->scope('foo', 'bar');
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeRawException()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->scope('foo')->raw('stdClass');
    }

    /**
     * @depends testConstructAndRaw
     * @depends testSetWithoutNames
     * @depends testAliasException
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeAliasException()
    {
        $dim = new Dim;
        $dim->set(new stdClass);
        $dim->scope('foo')->alias('stdClass', 'foo');
    }
}
 