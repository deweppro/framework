<?php

namespace Dewep\Tests;

use Dewep\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testCheckContainer()
    {
        Container::reset();

        self::assertFalse(Container::has('test'));

        Container::set('test', 1);
        self::assertEquals(Container::get('test'), 1);

        Container::remove('test');
        self::assertFalse(Container::has('test'));

        Container::set('test', 1);
        Container::reset();
        self::assertFalse(Container::has('test'));
    }

}