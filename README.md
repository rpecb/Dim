# Dim
*The PHP dependency injection manager*

Dim is a small, simple and powerful Dependency Injection Container for PHP 5.3 or later.
```php
class One { /* ... */ }

class Two { /* ... */ }

class Three
{
    public function __construct(One $one, Two $two)
    {
        // ...
    }
}

// Instantiates the container
$container = new Container;

// Puts service that creates an instance of "Three" to the container
$container->set(new Service('Three'));

// Puts service that creates an instance of "One" to the container
$container->set(new Service('One'));

// Puts instance of "Two" to the container
$container->set(new Two);

// ...

// Instantiates "Three" passing dependencies "One" and "Two" to the constructor
$three = $container->get('Three');
```

## Installation
You may install the Dim with [Composer](https://getcomposer.org). Create a `composer.json` file in your project
root and run the `php composer.phar install` command to install it:
```php
{
    "require": {
        "GR3S/Dim": "1.*"
    }
}
```
Add this line to your application’s index.php file:
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
```
Alternatively, you can download the [archive](https://github.com/GR3S/Dim/archive/master.zip) and extract it.

Creating a container is a matter of instating the `Container` class:
```php
$container = new Container;
```
As many other dependency injection containers, Dim is able to manage two different kind of data: services and parameters.

## Defining Parameters
Defining a parameter is as simple as using the `Container` instance as an array:
```php
$container->set
```


## Tests
To run the test suite, you need [PHPUnit](http://phpunit.de):
```bash
$ php composer.phar install --dev
$ vendor/bin/phpunit
```

## License
Dim is licensed under the MIT license.