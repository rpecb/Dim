<?php

namespace Dim;

interface ServiceInterface
{
    public function getClass();

    public function get();

    public function __invoke();
} 