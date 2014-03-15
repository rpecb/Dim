<?php

class Foo
{
    public static function factory()
    {
        return new static;
    }
}

class Poo
{
    protected function __construct()
    {
    }
}