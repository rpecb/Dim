<?php

class Singleton extends Service
{
    protected $instance;

    public function get($arguments = array(), Dim $dim = null) #
    {
        if ($this->instance === null) {
            $this->instance = parent::get($arguments, $dim);
        }
        return $this->instance;
    }
} 