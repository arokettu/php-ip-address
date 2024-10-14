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

    public function testSingleIPBlock(): void
    {
        $ip4 = IPv4Address::fromString('127.0.0.1');
        $ip6 = IPv6Address::fromString('::1');

        self::assertEquals('127.0.0.1/32', (string)$ip4->toBlock());
        self::assertEquals('::1/128', (string)$ip6->toBlock());
    }

    public function testLargerBlock(): void
    {
        $ip4 = new IPv4Address('abcd');
        $ip6 = new IPv6Address('abcdabcdabcdabcd');

        self::assertEquals('97.0.0.0/8', (string)$ip4->toBlock(8));
        self::assertEquals('6162:6364::/32', (string)$ip6->toBlock(32));
    }
}
