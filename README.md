# Dim, a PHP dependency injection manager
Dim is a small, simple and powerful Dependency Injection Container for PHP 5.3 or later.
```php
class One
{
    //...
}

class Two
{
    //...
}

class Three
{
    public function __construct(One $one, Two $two)
    {
        //...
    }
}

$container = new Container; // Creates an instance of the container
$container->three = new Service('Three'); // Creates an association between «three» and service creates an instance of «Three»
$container->one = new Service('One'); // Creates an association between «one» and service creates an instance of «One»
$container->two = new Two; // Creates an association between «one» and instance of «One»
//...
$three = $container->three; // Instantiates Three with passed dependencies One and Two to the constructor
```

## Installation
You may install the Dim with [Composer](https://getcomposer.org). Just create a `composer.json` file in your project
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