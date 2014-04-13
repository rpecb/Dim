<?php

namespace Dim\Service;

use Dim\Container;
use Dim\Service;

class Singleton extends Service
{
    protected $instance;

    public function get($arguments = null, Container $dim = null)
    {
        if ($this->instance === null) {
            $this->instance = parent::get($arguments, $dim);
        }
        return $this->instance;
    }
} 