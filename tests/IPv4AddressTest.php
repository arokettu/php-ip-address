<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use PHPUnit\Framework\TestCase;

class IPv4AddressTest extends TestCase
{
    public function testCreation(): void
    {
        $ip = new IPv4Address("\x7f\x00\x00\x01");

        self::assertEquals('127.0.0.1', $ip->toString());
    }
}
