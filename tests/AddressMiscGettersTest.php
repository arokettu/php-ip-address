<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use PHPUnit\Framework\TestCase;

class AddressMiscGettersTest extends TestCase
{
    public function testBytes(): void
    {
        $ip4 = new IPv4Address('abcd');
        $ip6 = new IPv6Address('abcdabcdabcdabcd');

        self::assertEquals('abcd', $ip4->getBytes());
        self::assertEquals('abcdabcdabcdabcd', $ip6->getBytes());
    }

    public function testRange(): void
    {
        $ip4 = IPv4Address::fromString('127.0.0.1');
        $ip6 = IPv6Address::fromString('::1');

        self::assertEquals('127.0.0.1/32', (string)$ip4->toRange());
        self::assertEquals('::1/128', $ip6->toRange());
    }
}
