# Dim – PHP Dependency Injection Manager
Dim is a small, simple and powerful Dependency Injection Container for PHP:
```php
use Dim\Container;
use Dim\Service;

class One { /* ... */ }

class Two { /* ... */ }

class Foo
{
    public function __construct(One $one, Two $two, $three)
    {
        // ...
    }
    // ...
}

// Instantiates the container
$container = new Container;
// Puts service that creates an instance of "One" to the container
$container->set(new Service('One'));
// Puts instance of "Two" to the container
$container->set(new Two);
// Puts service that creates an instance of "Foo" to the container
$container->set(new Service('Foo'));
// ...
// Instantiates "Foo" passing dependencies "One", "Two" and third argument "3" to the constructor
$three = $container->get('Foo', array('three' => 3));
```
Dim works with PHP 5.3 or later.

## Installation
You may install the Dim with [Composer](https://getcomposer.org).

1. Create a `composer.json` file in your project root
and run the `php composer.phar install` command to install it:
    ```php
    {
        "require": {
            "GR3S/Dim": "1.*"
        }
    }
    ```

2. Add this line to your application’s index.php file:
    ```php
    <?php
    require_once __DIR__ . '/vendor/autoload.php';
    ```

3. Instantiate the `Container` class:
    ```php
    $container = new Dim\Container;
    ```

> Alternatively, you can download the [archive](https://github.com/GR3S/Dim/archive/master.zip) and extract it.

## Defining parameters
```php
$container->set('value' , 'name');
// or
$container->set('value' , array('name1', 'name2'));
// or
$container->name = 'value';
// or
$container['name'] = 'value';
```
> For objects you can omit the definition of names, in this case for names will be used class name of object, names of
extended classes and interfaces and names of used traits.

## Defining services
```php
$container->set(new Service('Foo'));
// or
$container->set(new Service('Foo') , array('name1', 'name2'));
// or
$container->Foo = new Service('Foo');
// or
$container['Foo'] = new Service('Foo');
```
> In first case for names will be used class name, names of extended classes and interfaces and names of used traits.

You can define arguments that will be passed to constructor of service class:
```php
$container->set(
    new Service('Foo', array(new One, new Two, 3))
);
// or
$container->set(
    new Service('Foo', array('one' => new One, 'two' => new Two, 'three' => 3))
);
// or
$container->set(
    new Service('Foo', array(0 => new One, 'two' => new Two, 2 => 3))
);
// ...
```
> Keys should be identical to parameter names or their positions in constructor definition.

## Defining aliases
If you need to add an additional name for defined parameter or service you can use `alias` method:
```php
$container->Foo = new Service('Foo');
$container->alias('Foo', 'alias');
// or
$container->alias('Foo', array('alias1', 'alias2'));
```

## Retrieving data
```php
$foo = $container->get('Foo');
// or
$foo = $container->Foo;
// or
$foo = $container['Foo'];
// or
$foo = $container('Foo');
```
Also you can pass additional arguments to constructor of service class, they overwrite arguments with same keys passed
to `Service` constructor:
```php
$foo = $container->get('Foo', array('three' => 'three'));
// or
$foo = $container('Foo', array('three' => 'three'));
```

## Scopes
If you wish to limit the scope for dependencies you can use scopes:
```php
$container->scope('foo')->set(new Service('One'));
$container->scope('foo')->set(new Two);
$container->scope('foo')->set(new Service('Foo'));
// Service "Foo" will look for dependencies in scope "foo"
$foo1 = $container->scope('foo')->Foo;
// ...
$container->scope('bar')->set(new Service('One'));
$container->scope('bar')->set(new Two);
$container->scope('bar')->set(new Service('Foo'));
// Service "Foo" will look for dependencies in scope "bar"
$foo1 = $container->scope('bar')->Foo;
```
Also you can group actions in scope:
```php
$container->scope('foo', function () use ($container) {
    $container->set(new Service('One'));
    $container->set(new Two);
    $container->set(new Service('Foo'));
});
```

## Kinds of services
### Service
*Class: Dim\Service*

Creates and returns new instance of class:
```php
$container->foo = new Service('Foo');
$foo1 = $container->foo;
$foo2 = $container->foo;
```
> `$foo1` and `$foo2` are different instances.

### Singleton
*Class: Dim\Service\Singleton*

Once creates an instance of class and always returns the same instance for all calls:
```php
$container->foo = new Singleton('Foo');
$foo1 = $container->foo;
$foo2 = $container->foo;
```
> `$foo1` and `$foo2` are the same.

### Factory
*Class: Dim\Service\Factory*

Returns new instance of class created by function or factory method:
```php
$container->foo = new Factory('Foo', function () {
    return new Foo;
});
$foo = $container->foo;
```

### Extension
*Class: Dim\Service\Extension*

Extends creation of instance by other service:
```php
$container->foo = new Service('Foo');
$container->bar = new Extension($container->foo, function (Foo $foo) {
    $foo->property = 'value';
    $foo->callMethod();
    // ...
    return $foo;
});
$bar = $container->bar;
```

## Other actions
#### Get raw data from container:
*Method: Dim\Container::raw*
```php
$container->foo = new Service('Foo');
$foo = $container->raw('foo');
```
> `$foo` will contains an instance of `Service` class.

#### Check that parameter or service is defined:
*Methods:*
* *Dim\Container::has*
* *Dim\Container::offsetExists*
* *Dim\Container::__isset*
```php
$container->foo = new Service('Foo');
$foo = $container->has('foo');
// or
$foo = isset($container['foo']);
// or
$foo = isset($container->foo);
// ...
$bar = $container->has('bar');
```
> `$foo` will contains `true`, `$bar` will contains `false`

#### Remove parameter or service from container:
*Methods:*
* *Dim\Container::remove*
* *Dim\Container::offsetUnset*
* *Dim\Container::__unset*
```php
$container->remove('foo');
// or
unset($container['foo']);
// or
unset($container->foo);
```
#### Remove all parameters and services from container:
*Method: Dim\Container::clear*
```php
$container->clear();
```

## Tests
To run the test suite, you need [PHPUnit](http://phpunit.de):
```bash
$ php composer.phar install --dev
$ vendor/bin/phpunit
```

## License
Dim is licensed under the MIT license.