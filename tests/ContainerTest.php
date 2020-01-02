<?php

declare(strict_types=1);

namespace Dewep\Tests;

use Dewep\Container;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testCheckContainer(): void
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
