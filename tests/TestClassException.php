<?php

namespace Kregel\ExceptionProbe\Tests;

/**
 * Class TestClassException
 * @package Kregel\ExceptionProbe\Tests
 */
class TestClassException
{
    /**
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        throw new \InvalidArgumentException('You must provide a method for the things');
    }
}
