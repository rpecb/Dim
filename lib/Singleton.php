<?php

class Singleton extends Service
{
    protected $instance;

    public function get($arguments = array()) #
    {
        if ($this->instance === null) {
            $this->instance = parent::get($arguments);
        }
        return $this->instance;
    }
} 