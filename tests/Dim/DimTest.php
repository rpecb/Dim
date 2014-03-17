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
    public function testAddWithoutNames()
    {
        $dim = new Dim;
        $dim->add('ArrayObject');
        $foo = $dim->raw('ArrayObject');
        $bar = $dim->raw('ArrayAccess');
        $foobar = $dim->raw('Countable');
        $this->assertInstanceOf('Service', $foo);
        $this->assertInstanceOf('Service', $bar);
        $this->assertInstanceOf('Service', $foobar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testAddWithName()
    {
        $dim = new Dim;
        $dim->add('stdClass', 'std');
        $this->assertInstanceOf('Service', $dim->raw('std'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testAddWithNames()
    {
        $dim = new Dim;
        $dim->add('stdClass', array('foo', 'bar'));
        $foo = $dim->raw('foo');
        $bar = $dim->raw('bar');
        $this->assertInstanceOf('Service', $foo);
        $this->assertInstanceOf('Service', $bar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSingletonWithoutNames()
    {
        $dim = new Dim;
        $dim->singleton('stdClass');
        $this->assertInstanceOf('Singleton', $dim->raw('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSingletonWithName()
    {
        $dim = new Dim;
        $dim->singleton('stdClass', 'foo');
        $this->assertInstanceOf('Singleton', $dim->raw('foo'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSingletonWithNames()
    {
        $dim = new Dim;
        $dim->singleton('stdClass', array('foo', 'bar'));
        $foo = $dim->raw('foo');
        $bar = $dim->raw('bar');
        $this->assertInstanceOf('Singleton', $foo);
        $this->assertInstanceOf('Singleton', $bar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testInstanceWithoutNames()
    {
        $dim = new Dim;
        $foo = new ArrayObject;
        $dim->instance($foo);
        $this->assertSame($foo, $dim->raw('ArrayObject'));
        $this->assertSame($foo, $dim->raw('ArrayAccess'));
        $this->assertSame($foo, $dim->raw('Countable'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testInstanceWithName()
    {
        $dim = new Dim;
        $foo = new ArrayObject;
        $dim->instance($foo, 'foo');
        $this->assertSame($foo, $dim->raw('foo'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testInstanceWithNames()
    {
        $dim = new Dim;
        $foo = new ArrayObject;
        $dim->instance($foo, array('foo', 'bar'));
        $this->assertSame($foo, $dim->raw('foo'));
        $this->assertSame($foo, $dim->raw('bar'));
    }

    /**
     * @depends testConstructAndRaw
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage An instance expected.
     */
    public function testInstanceException()
    {
        $dim = new Dim;
        $dim->instance('Child');
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testFactoryWithName()
    {
        $dim = new Dim;
        $dim->factory(
            function () {
                return new stdClass;
            },
            'foo'
        );
        $this->assertInstanceOf('Factory', $dim->raw('foo'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testFactoryWithNames()
    {
        $dim = new Dim;
        $dim->factory(
            function () {
                return new stdClass;
            },
            array('foo', 'bar')
        );
        $foo = $dim->raw('foo');
        $bar = $dim->raw('bar');
        $this->assertInstanceOf('Factory', $foo);
        $this->assertInstanceOf('Factory', $bar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     */
    public function testExtendWithName()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->extend(
            'stdClass',
            function (stdClass $foo) {
                return $foo;
            }
        );
        $foo = $dim->raw('stdClass');
        $this->assertInstanceOf('Extended', $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     */
    public function testExtendWithNames()
    {
        $dim = new Dim;
        $dim->add('ArrayObject');
        $dim->extend(
            array('ArrayObject', 'ArrayAccess'),
            function (ArrayObject $foo) {
                return $foo;
            }
        );
        $foo = $dim->raw('ArrayObject');
        $bar = $dim->raw('ArrayAccess');
        $foobar = $dim->raw('Countable');
        $this->assertInstanceOf('Extended', $foo);
        $this->assertInstanceOf('Extended', $bar);
        $this->assertInstanceOf('Service', $foobar);
        $this->assertSame($foo, $bar);
    }

    /**
     * @depends testConstructAndRaw
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency foo is not defined in current scope.
     */
    public function testExtendException()
    {
        $dim = new Dim;
        $dim->extend(
            'foo',
            function ($foo) {
                return $foo;
            }
        );
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     */
    public function testAlias()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->alias('stdClass', 'foo');
        $foo1 = $dim->raw('stdClass');
        $foo2 = $dim->raw('foo');
        $this->assertInstanceOf('Service', $foo1);
        $this->assertInstanceOf('Service', $foo2);
        $this->assertSame($foo1, $foo2);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     */
    public function testAliases()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->alias('stdClass', array('foo', 'bar'));
        $foo = $dim->raw('stdClass');
        $foo1 = $dim->raw('foo');
        $bar = $dim->raw('bar');
        $this->assertInstanceOf('Service', $foo);
        $this->assertInstanceOf('Service', $foo1);
        $this->assertInstanceOf('Service', $bar);
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
     * @depends testAddWithoutNames
     */
    public function testHas()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $this->assertTrue($dim->has('stdClass'));
        $this->assertFalse($dim->has('Foo'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testRemove()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->remove('stdClass');
        $dim->remove('Foo');
        $this->assertFalse($dim->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testClear()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->add('ArrayObject');
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
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testOffsetExists()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $this->assertTrue(isset($dim['stdClass']));
        $this->assertFalse(isset($dim['Foo']));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     */
    public function testOffsetSet()
    {
        $dim = new Dim;
        $dim['stdClass'] = 'stdClass';
        $this->assertInstanceOf('Service', $dim->raw('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testRemove
     * @depends testHas
     */
    public function testOffsetUnset()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        unset($dim['stdClass']);
        unset($dim['Foo']);
        $this->assertFalse($dim->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testIsset()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $this->assertTrue(isset($dim->stdClass));
        $this->assertFalse(isset($dim->Foo));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     */
    public function testSet()
    {
        $dim = new Dim;
        $dim->foo = 'stdClass';
        $this->assertInstanceOf('Service', $dim->raw('foo'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testRemove
     * @depends testHas
     */
    public function testUnset()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        unset($dim->stdClass);
        unset($dim->Foo);
        $this->assertFalse($dim->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     * @depends testSingletonWithName
     * @depends testFactoryWithName
     * @depends testExtendWithName
     */
    public function testGet()
    {
        $dim = new Dim;
        $dim->add('stdClass', 'foo');
        $dim->singleton('stdClass', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim->get('Dim'));
        $foo = $dim->get('foo');
        $bar1 = $dim->get('bar');
        $bar2 = $dim->get('bar');
        $foobar = $dim->get('foobar');
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar1);
        $this->assertInstanceOf('stdClass', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertSame($bar1, $bar2);
        $dim->extend(
            'foo',
            function (stdClass $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim->get('foo');
        $this->assertInstanceOf('stdClass', $extendedFoo);
        $this->assertNotSame($extendedFoo, $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     * @depends testSingletonWithName
     * @depends testFactoryWithName
     * @depends testExtendWithName
     * @depends testGet
     */
    public function testOffsetGet()
    {
        $dim = new Dim;
        $dim->add('stdClass', 'foo');
        $dim->singleton('stdClass', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim['Dim']);
        $foo = $dim['foo'];
        $bar1 = $dim['bar'];
        $bar2 = $dim['bar'];
        $foobar = $dim['foobar'];
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar1);
        $this->assertInstanceOf('stdClass', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertSame($bar1, $bar2);
        $dim->extend(
            'foo',
            function (stdClass $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim['foo'];
        $this->assertInstanceOf('stdClass', $extendedFoo);
        $this->assertNotSame($extendedFoo, $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     * @depends testSingletonWithName
     * @depends testFactoryWithName
     * @depends testExtendWithName
     * @depends testGet
     */
    public function testInvoke()
    {
        $dim = new Dim;
        $dim->add('stdClass', 'foo');
        $dim->singleton('stdClass', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim('Dim'));
        $foo = $dim('foo');
        $bar1 = $dim('bar');
        $bar2 = $dim('bar');
        $foobar = $dim('foobar');
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar1);
        $this->assertInstanceOf('stdClass', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertSame($bar1, $bar2);
        $dim->extend(
            'foo',
            function (stdClass $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim('foo');
        $this->assertInstanceOf('stdClass', $extendedFoo);
        $this->assertNotSame($extendedFoo, $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     * @depends testSingletonWithName
     * @depends testFactoryWithName
     * @depends testExtendWithName
     * @depends testGet
     */
    public function testMagicGet()
    {
        $dim = new Dim;
        $dim->add('stdClass', 'foo');
        $dim->singleton('stdClass', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim->Dim);
        $foo = $dim->foo;
        $bar1 = $dim->bar;
        $bar2 = $dim->bar;
        $foobar = $dim->foobar;
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar1);
        $this->assertInstanceOf('stdClass', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertSame($bar1, $bar2);
        $dim->extend(
            'foo',
            function (stdClass $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim->foo;
        $this->assertInstanceOf('stdClass', $extendedFoo);
        $this->assertNotSame($extendedFoo, $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     * @depends testOffsetSet
     * @depends testSet
     * @depends testInstanceWithName
     * @depends testSingletonWithName
     * @depends testFactoryWithName
     * @depends testExtendWithName
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
        $dim->scope('foo')->add('stdClass', 'foo1');
        $scope = $dim->scope('foo');
        $scope['foobar1'] = 'stdClass';
        $dim->scope('foo')->foobar2 = 'stdClass';
        $dim->scope('foo')->instance(new stdClass, 'stdclass');
        $dim->scope('foo')->singleton('stdClass', 'foo2');
        $dim->scope('foo')->factory('Foo::factory', 'foo3');
        $dim->scope('foo')->extend(
            'foo3',
            function ($foo) {
                return $foo;
            }
        );

        $dim->scope('foo')->alias('stdclass', 'std');

        $this->assertFalse($dim->has('foo1'));
        $this->assertFalse($dim->has('foobar1'));
        $this->assertFalse($dim->has('foobar2'));
        $this->assertFalse($dim->has('stdclass'));
        $this->assertFalse($dim->has('foo2'));
        $this->assertFalse($dim->has('foo3'));
        $this->assertFalse($dim->has('std'));
        $this->assertTrue($dim->scope('foo')->has('foo1'));
        $this->assertTrue($dim->scope('foo')->has('foobar1'));
        $this->assertTrue($dim->scope('foo')->has('foobar2'));
        $this->assertTrue($dim->scope('foo')->has('stdclass'));
        $this->assertTrue($dim->scope('foo')->has('foo2'));
        $this->assertTrue($dim->scope('foo')->has('foo3'));
        $this->assertTrue($dim->scope('foo')->has('std'));

        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foo1']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foobar1']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foobar2']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['stdclass']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foo2']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foo3']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['std']));

        $this->assertTrue(isset($dim->scope('foo')->foo1));
        $this->assertTrue(isset($dim->scope('foo')->foobar1));
        $this->assertTrue(isset($dim->scope('foo')->foobar2));
        $this->assertTrue(isset($dim->scope('foo')->stdclass));
        $this->assertTrue(isset($dim->scope('foo')->foo2));
        $this->assertTrue(isset($dim->scope('foo')->foo3));
        $this->assertTrue(isset($dim->scope('foo')->std));

        $this->assertInstanceOf('Service', $dim->scope('foo')->raw('foo1'));
        $this->assertInstanceOf('Service', $dim->scope('foo')->raw('foobar1'));
        $this->assertInstanceOf('Service', $dim->scope('foo')->raw('foobar2'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('stdclass'));
        $this->assertInstanceOf('Singleton', $dim->scope('foo')->raw('foo2'));
        $this->assertInstanceOf('Extended', $dim->scope('foo')->raw('foo3'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->raw('std'));

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('foo1'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('foobar1'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('foobar2'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('stdclass'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('foo2'));
        $this->assertInstanceOf('Foo', $dim->scope('foo')->get('foo3'));
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->get('std'));

        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['foo1']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['foobar1']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['foobar2']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['stdclass']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['foo2']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope['foo3']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope['std']);

        $this->assertInstanceOf('stdClass', $dim->scope('foo')->foo1);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->foobar1);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->foobar2);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->stdclass);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->foo2);
        $this->assertInstanceOf('Foo', $dim->scope('foo')->foo3);
        $this->assertInstanceOf('stdClass', $dim->scope('foo')->std);

        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('foo1'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('foobar1'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('foobar2'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('stdclass'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('foo2'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope('foo3'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('stdClass', $scope('std'));

        $this->assertNotSame($dim->scope('foo')->get('foo1'), $dim->scope('foo')->get('foo1'));
        $this->assertNotSame($dim->scope('foo')->get('foobar1'), $dim->scope('foo')->get('foobar1'));
        $this->assertNotSame($dim->scope('foo')->get('foobar2'), $dim->scope('foo')->get('foobar2'));
        $this->assertNotSame($dim->scope('foo')->get('foo3'), $dim->scope('foo')->get('foo3'));
        $this->assertSame($dim->scope('foo')->get('stdclass'), $dim->scope('foo')->get('stdclass'));
        $this->assertSame($dim->scope('foo')->get('foo2'), $dim->scope('foo')->get('foo2'));
        $this->assertSame($dim->scope('foo')->get('std'), $dim->scope('foo')->get('stdclass'));

        $dim->scope('foo')->remove('std');
        $scope = $dim->scope('foo');
        unset($scope['stdclass']);
        unset($dim->scope('foo')->foo3);
        $this->assertFalse($dim->scope('foo')->has('std'));
        $this->assertFalse($dim->scope('foo')->has('stdclass'));
        $this->assertFalse($dim->scope('foo')->has('foo3'));

        $dim->scope('foo')->clear();
        $this->assertFalse($dim->scope('foo')->has('foo1'));
        $this->assertFalse($dim->scope('foo')->has('foobar1'));
        $this->assertFalse($dim->scope('foo')->has('foobar2'));
        $this->assertFalse($dim->scope('foo')->has('foo2'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testScope
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testSubScope()
    {
        $dim = new Dim;
        $dim->scope('foo')->scope('bar')->add('stdClass');
        $this->assertFalse($dim->has('stdClass'));
        $this->assertFalse($dim->scope('foo')->has('stdClass'));
        $this->assertFalse($dim->scope('bar')->has('stdClass'));
        $this->assertTrue($dim->scope('foo')->scope('bar')->has('stdClass'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testScope
     * @depends testAddWithName
     * @depends testInstanceWithName
     * @depends testHas
     */
    public function testScopeWithCallable()
    {
        $dim = new Dim;
        $dim->scope(
            'foo',
            function () use ($dim) {
                $dim->add('stdClass', 'std1');
                $dim->instance(new stdClass, 'std2');
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
     * @depends testAddWithoutNames
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeRawException()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->scope('foo')->raw('stdClass');
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testExtendException
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeExtendException()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->scope('foo')->extend(
            'stdClass',
            function ($foo) {
                return $foo;
            }
        );
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testAliasException
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency stdClass is not defined in current scope.
     */
    public function testScopeAliasException()
    {
        $dim = new Dim;
        $dim->add('stdClass');
        $dim->scope('foo')->alias('stdClass', 'foo');
    }
}
 