<?php

class Foo
{
    public static function factory()
    {
        return new static;
    }

    protected static function bar()
    {
        return new static;
    }

    public function __invoke()
    {
        return static::factory();
    }
}

class Poo
{
    protected function __construct()
    {
    }
}