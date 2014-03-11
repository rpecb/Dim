<?php

class Singleton extends Service
{
    public function get($arguments = array())
    {
        static $instance = null;
        if ($instance === null) {
            $instance = parent::get($arguments);
        }
        return $instance;
    }
} 