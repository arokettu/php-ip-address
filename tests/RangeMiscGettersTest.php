<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Range;
use PHPUnit\Framework\TestCase;

class RangeMiscGettersTest extends TestCase
{
    public function testFirstLast(): void
    {
        $range4 = IPv4Range::fromString('157.240.0.0/16');
        $range6 = IPv6Range::fromString('2a03:2880::/29');

        self::assertEquals('157.240.0.0', (string)$range4->getFirstAddress());
        self::assertEquals('157.240.255.255', (string)$range4->getLastAddress());

        self::assertEquals('2a03:2880::', (string)$range6->getFirstAddress());
        self::assertEquals('2a03:2887:ffff:ffff:ffff:ffff:ffff:ffff', (string)$range6->getLastAddress());
    }

    public function testBaseGetters(): void
    {
        $range4 = IPv4Range::fromString('157.240.0.0/16');
        $range6 = IPv6Range::fromString('2a03:2880::/29');

        self::assertEquals('9df00000', bin2hex($range4->getBytes()));
        self::assertEquals('ffff0000', bin2hex($range4->getMaskBytes()));
        self::assertEquals('255.255.0.0', $range4->getMaskString());
        self::assertEquals(16, $range4->getPrefix());

        self::assertEquals('2a032880000000000000000000000000', bin2hex($range6->getBytes()));
        self::assertEquals('fffffff8000000000000000000000000', bin2hex($range6->getMaskBytes()));
        self::assertEquals('ffff:fff8::', $range6->getMaskString());
        self::assertEquals(29, $range6->getPrefix());
    }
}
