<?php

class DimTest extends PHPUnit_Framework_TestCase
{
    public function testConstructAndRaw()
    {
        $dim = new Dim;
        $childDim = new ChildDim;
        $this->assertSame($dim, $dim->raw('Dim'));
        $this->assertSame($childDim, $childDim->raw('ChildDim'));
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
        $dim->add('ChildDim');
        $foo = $dim->raw('ChildDim');
        $bar = $dim->raw('Dim');
        $foobar = $dim->raw('ArrayAccess');
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
        $dim->add('ChildDim', 'dim');
        $this->assertInstanceOf('Service', $dim->raw('dim'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testAddWithNames()
    {
        $dim = new Dim;
        $dim->add('ChildDim', array('foo', 'bar'));
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
        $dim->singleton('Foo');
        $this->assertInstanceOf('Singleton', $dim->raw('Foo'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSingletonWithName()
    {
        $dim = new Dim;
        $dim->singleton('Foo', 'foo');
        $this->assertInstanceOf('Singleton', $dim->raw('foo'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testSingletonWithNames()
    {
        $dim = new Dim;
        $dim->singleton('Foo', array('foo', 'bar'));
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
        $dim->instance(new ChildDim);
        $this->assertSame($dim->raw('ChildDim'), $dim->raw('Dim'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testInstanceWithName()
    {
        $dim = new Dim;
        $childDim = new ChildDim;
        $dim->instance($childDim, 'childdim');
        $this->assertSame($childDim, $dim->raw('childdim'));
    }

    /**
     * @depends testConstructAndRaw
     */
    public function testInstanceWithNames()
    {
        $dim = new Dim;
        $childDim = new ChildDim;
        $dim->instance($childDim, array('foo', 'bar'));
        $this->assertSame($childDim, $dim->raw('foo'));
        $this->assertSame($childDim, $dim->raw('bar'));
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
                return new Foo;
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
                return new Foo;
            },
            array('foo', 'bar')
        );
        $foo = $dim->raw('foo');
        $bar = $dim->raw('foo');
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
        $dim->add('Foo');
        $dim->extend(
            'Foo',
            function (Foo $foo) {
                return $foo;
            }
        );
        $foo = $dim->raw('Foo');
        $this->assertInstanceOf('Extended', $foo);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     */
    public function testExtendWithNames()
    {
        $dim = new Dim;
        $dim->add('ChildDim');
        $dim->extend(
            array('ChildDim', 'Dim'),
            function (Dim $foo) {
                return $foo;
            }
        );
        $foo = $dim->raw('ChildDim');
        $bar = $dim->raw('Dim');
        $foobar = $dim->raw('ArrayAccess');
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
        $dim->add('Foo');
        $dim->alias('Foo', 'foo');
        $foo1 = $dim->raw('Foo');
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
        $dim->add('Foo');
        $dim->alias('Foo', array('bar1', 'bar2'));
        $foo = $dim->raw('Foo');
        $bar1 = $dim->raw('bar1');
        $bar2 = $dim->raw('bar2');
        $this->assertInstanceOf('Service', $foo);
        $this->assertInstanceOf('Service', $bar1);
        $this->assertInstanceOf('Service', $bar2);
        $this->assertSame($foo, $bar1);
        $this->assertSame($bar1, $bar2);
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
        $dim->add('Foo');
        $this->assertTrue($dim->has('Foo'));
        $this->assertFalse($dim->has('Bar'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testRemove()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $dim->remove('Foo');
        $dim->remove('Bar');
        $this->assertFalse($dim->has('Foo'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testClear()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $dim->add('ChildDim');
        $dim->clear();
        $this->assertFalse($dim->has('Foo'));
        $this->assertFalse($dim->has('ChildDim'));
        $this->assertFalse($dim->has('Dim'));
        $this->assertFalse($dim->has('ArrayAccess'));
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
        $names = $getNames->invokeArgs($dim, array('ChildDim'));
        $this->assertCount(3, $names);
        $this->assertContains('ChildDim', $names);
        $this->assertContains('Dim', $names);
        $this->assertContains('ArrayAccess', $names);
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testOffsetExists()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $this->assertTrue(isset($dim['Foo']));
        $this->assertFalse(isset($dim['Bar']));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     */
    public function testOffsetSet()
    {
        $dim = new Dim;
        $dim['Foo'] = 'Foo';
        $this->assertInstanceOf('Service', $dim->raw('Foo'));
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
        $dim->add('Foo');
        unset($dim['Foo']);
        unset($dim['Bar']);
        $this->assertFalse($dim->has('Foo'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testHas
     */
    public function testIsset()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $this->assertTrue(isset($dim->Foo));
        $this->assertFalse(isset($dim->Bar));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithName
     */
    public function testSet()
    {
        $dim = new Dim;
        $dim->foo = 'Foo';
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
        $dim->add('Foo');
        unset($dim->Foo);
        unset($dim->Bar);
        $this->assertFalse($dim->has('Foo'));
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
        $dim->add('Foo', 'foo');
        $dim->singleton('Foo', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim->get('Dim'));
        $foo = $dim->get('foo');
        $bar1 = $dim->get('bar');
        $bar2 = $dim->get('bar');
        $foobar = $dim->get('foobar');
        $this->assertInstanceOf('Foo', $foo);
        $this->assertInstanceOf('Foo', $bar1);
        $this->assertInstanceOf('Foo', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertNotSame($foo, $foobar);
        $this->assertSame($bar1, $bar2);
        $this->assertNotSame($bar1, $foobar);
        $this->assertNotSame($bar2, $foobar);
        $dim->extend(
            'foo',
            function (Foo $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim->get('foo');
        $this->assertInstanceOf('Foo', $extendedFoo);
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
        $dim->add('Foo', 'foo');
        $dim->singleton('Foo', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim['Dim']);
        $foo = $dim['foo'];
        $bar1 = $dim['bar'];
        $bar2 = $dim['bar'];
        $foobar = $dim['foobar'];
        $this->assertInstanceOf('Foo', $foo);
        $this->assertInstanceOf('Foo', $bar1);
        $this->assertInstanceOf('Foo', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertNotSame($foo, $foobar);
        $this->assertSame($bar1, $bar2);
        $this->assertNotSame($bar1, $foobar);
        $this->assertNotSame($bar2, $foobar);
        $dim->extend(
            'foo',
            function (Foo $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim['foo'];
        $this->assertInstanceOf('Foo', $extendedFoo);
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
        $dim->add('Foo', 'foo');
        $dim->singleton('Foo', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim('Dim'));
        $foo = $dim('foo');
        $bar1 = $dim('bar');
        $bar2 = $dim('bar');
        $foobar = $dim('foobar');
        $this->assertInstanceOf('Foo', $foo);
        $this->assertInstanceOf('Foo', $bar1);
        $this->assertInstanceOf('Foo', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertNotSame($foo, $foobar);
        $this->assertSame($bar1, $bar2);
        $this->assertNotSame($bar1, $foobar);
        $this->assertNotSame($bar2, $foobar);
        $dim->extend(
            'foo',
            function (Foo $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim('foo');
        $this->assertInstanceOf('Foo', $extendedFoo);
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
        $dim->add('Foo', 'foo');
        $dim->singleton('Foo', 'bar');
        $dim->factory('Foo::factory', 'foobar');
        $this->assertSame($dim, $dim->Dim);
        $foo = $dim->foo;
        $bar1 = $dim->bar;
        $bar2 = $dim->bar;
        $foobar = $dim->foobar;
        $this->assertInstanceOf('Foo', $foo);
        $this->assertInstanceOf('Foo', $bar1);
        $this->assertInstanceOf('Foo', $bar2);
        $this->assertInstanceOf('Foo', $foobar);
        $this->assertNotSame($foo, $bar1);
        $this->assertNotSame($foo, $bar2);
        $this->assertNotSame($foo, $foobar);
        $this->assertSame($bar1, $bar2);
        $this->assertNotSame($bar1, $foobar);
        $this->assertNotSame($bar2, $foobar);
        $dim->extend(
            'foo',
            function (Foo $foo) {
                return $foo;
            }
        );
        $extendedFoo = $dim->foo;
        $this->assertInstanceOf('Foo', $extendedFoo);
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
        $dim->scope('foo')->add('Foo', 'foo1');
        $scope = $dim->scope('foo');
        $scope['foobar1'] = 'Foo';
        $dim->scope('foo')->foobar2 = 'Foo';
        $dim->scope('foo')->instance(new ChildDim, 'childdim');
        $dim->scope('foo')->singleton('Foo', 'foo2');
        $dim->scope('foo')->factory('Foo::factory', 'foo3');
        $dim->scope('foo')->extend(
            'foo3',
            function ($foo) {
                return $foo;
            }
        );

        $dim->scope('foo')->alias('childdim', 'dim');

        $this->assertFalse($dim->has('foo1'));
        $this->assertFalse($dim->has('foobar1'));
        $this->assertFalse($dim->has('foobar2'));
        $this->assertFalse($dim->has('childdim'));
        $this->assertFalse($dim->has('foo2'));
        $this->assertFalse($dim->has('foo3'));
        $this->assertFalse($dim->has('dim'));
        $this->assertTrue($dim->scope('foo')->has('foo1'));
        $this->assertTrue($dim->scope('foo')->has('foobar1'));
        $this->assertTrue($dim->scope('foo')->has('foobar2'));
        $this->assertTrue($dim->scope('foo')->has('childdim'));
        $this->assertTrue($dim->scope('foo')->has('foo2'));
        $this->assertTrue($dim->scope('foo')->has('foo3'));
        $this->assertTrue($dim->scope('foo')->has('dim'));

        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foo1']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foobar1']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foobar2']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['childdim']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foo2']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['foo3']));
        $scope = $dim->scope('foo');
        $this->assertTrue(isset($scope['dim']));

        $this->assertTrue(isset($dim->scope('foo')->foo1));
        $this->assertTrue(isset($dim->scope('foo')->foobar1));
        $this->assertTrue(isset($dim->scope('foo')->foobar2));
        $this->assertTrue(isset($dim->scope('foo')->childdim));
        $this->assertTrue(isset($dim->scope('foo')->foo2));
        $this->assertTrue(isset($dim->scope('foo')->foo3));
        $this->assertTrue(isset($dim->scope('foo')->dim));

        $this->assertInstanceOf('Service', $dim->scope('foo')->raw('foo1'));
        $this->assertInstanceOf('Service', $dim->scope('foo')->raw('foobar1'));
        $this->assertInstanceOf('Service', $dim->scope('foo')->raw('foobar2'));
        $this->assertInstanceOf('ChildDim', $dim->scope('foo')->raw('childdim'));
        $this->assertInstanceOf('Singleton', $dim->scope('foo')->raw('foo2'));
        $this->assertInstanceOf('Extended', $dim->scope('foo')->raw('foo3'));
        $this->assertInstanceOf('ChildDim', $dim->scope('foo')->raw('dim'));

        $this->assertInstanceOf('Foo', $dim->scope('foo')->get('foo1'));
        $this->assertInstanceOf('Foo', $dim->scope('foo')->get('foobar1'));
        $this->assertInstanceOf('Foo', $dim->scope('foo')->get('foobar2'));
        $this->assertInstanceOf('ChildDim', $dim->scope('foo')->get('childdim'));
        $this->assertInstanceOf('Foo', $dim->scope('foo')->get('foo2'));
        $this->assertInstanceOf('Foo', $dim->scope('foo')->get('foo3'));
        $this->assertInstanceOf('ChildDim', $dim->scope('foo')->get('dim'));

        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope['foo1']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope['foobar1']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope['foobar2']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('ChildDim', $scope['childdim']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope['foo2']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope['foo3']);
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('ChildDim', $scope['dim']);

        $this->assertInstanceOf('Foo', $dim->scope('foo')->foo1);
        $this->assertInstanceOf('Foo', $dim->scope('foo')->foobar1);
        $this->assertInstanceOf('Foo', $dim->scope('foo')->foobar2);
        $this->assertInstanceOf('ChildDim', $dim->scope('foo')->childdim);
        $this->assertInstanceOf('Foo', $dim->scope('foo')->foo2);
        $this->assertInstanceOf('Foo', $dim->scope('foo')->foo3);
        $this->assertInstanceOf('ChildDim', $dim->scope('foo')->dim);

        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope('foo1'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope('foobar1'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope('foobar2'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('ChildDim', $scope('childdim'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope('foo2'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('Foo', $scope('foo3'));
        $scope = $dim->scope('foo');
        $this->assertInstanceOf('ChildDim', $scope('dim'));

        $this->assertNotSame($dim->scope('foo')->get('foo1'), $dim->scope('foo')->get('foo1'));
        $this->assertNotSame($dim->scope('foo')->get('foobar1'), $dim->scope('foo')->get('foobar1'));
        $this->assertNotSame($dim->scope('foo')->get('foobar2'), $dim->scope('foo')->get('foobar2'));
        $this->assertNotSame($dim->scope('foo')->get('foo3'), $dim->scope('foo')->get('foo3'));
        $this->assertSame($dim->scope('foo')->get('childdim'), $dim->scope('foo')->get('childdim'));
        $this->assertSame($dim->scope('foo')->get('foo2'), $dim->scope('foo')->get('foo2'));
        $this->assertSame($dim->scope('foo')->get('dim'), $dim->scope('foo')->get('childdim'));

        $dim->scope('foo')->remove('dim');
        $scope = $dim->scope('foo');
        unset($scope['childdim']);
        unset($dim->scope('foo')->foo3);
        $this->assertFalse($dim->scope('foo')->has('dim'));
        $this->assertFalse($dim->scope('foo')->has('childdim'));
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
        $dim->scope('foo')->scope('bar')->add('Foo');
        $this->assertFalse($dim->has('Foo'));
        $this->assertFalse($dim->scope('foo')->has('Foo'));
        $this->assertFalse($dim->scope('bar')->has('Foo'));
        $this->assertTrue($dim->scope('foo')->scope('bar')->has('Foo'));
    }

    /**
     * @depends testConstructAndRaw
     * @depends testScope
     * @depends testAddWithoutNames
     * @depends testInstanceWithoutNames
     * @depends testHas
     */
    public function testScopeWithCallable()
    {
        $dim = new Dim;
        $dim->scope(
            'foo',
            function () use ($dim) {
                $dim->add('Foo');
                $dim->instance(new ChildDim);
            }
        );
        $this->assertFalse($dim->has('Foo'));
        $this->assertFalse($dim->has('ChildDim'));
        $this->assertTrue($dim->scope('foo')->has('Foo'));
        $this->assertTrue($dim->scope('foo')->has('ChildDim'));
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
     * @expectedExceptionMessage Dependency Foo is not defined in current scope.
     */
    public function testScopeRawException()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $dim->scope('foo')->raw('Foo');
    }

    /**
     * @depends testConstructAndRaw
     * @depends testAddWithoutNames
     * @depends testExtendException
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Dependency Foo is not defined in current scope.
     */
    public function testScopeExtendException()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $dim->scope('foo')->extend(
            'Foo',
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
     * @expectedExceptionMessage Dependency Foo is not defined in current scope.
     */
    public function testScopeAliasException()
    {
        $dim = new Dim;
        $dim->add('Foo');
        $dim->scope('foo')->alias('Foo', 'Foo1');
    }
}
 