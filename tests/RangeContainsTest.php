<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Range;
use PHPUnit\Framework\TestCase;
use TypeError;

class RangeContainsTest extends TestCase
{
    public function testV4(): void
    {
        $ip1 = IPv4Address::fromString('192.168.1.100');
        $ip2 = IPv4Address::fromString('127.0.0.1');

        $range1 = IPv4Range::fromString('127.0.0.0/8');
        $range2 = IPv4Range::fromString('192.168.1.0/24');
        $range3 = IPv4Range::fromString('192.168.0.0/16');
        $range4 = IPv4Range::fromString('0.0.0.0/0'); // all addresses

        // ip 1
        self::assertFalse($range1->contains($ip1));
        self::assertTrue($range2->contains($ip1));
        self::assertTrue($range3->contains($ip1));
        self::assertTrue($range4->contains($ip1));

        // ip 2
        self::assertTrue($range1->contains($ip2));
        self::assertFalse($range2->contains($ip2));
        self::assertFalse($range3->contains($ip2));
        self::assertTrue($range4->contains($ip2));

        // range 1
        self::assertTrue($range1->contains($range1)); // self
        self::assertFalse($range2->contains($range1));
        self::assertFalse($range3->contains($range1));
        self::assertTrue($range4->contains($range1));

        // range 2
        self::assertFalse($range1->contains($range2));
        self::assertTrue($range2->contains($range2)); // self
        self::assertTrue($range3->contains($range2));
        self::assertTrue($range4->contains($range2));

        // range 3
        self::assertFalse($range1->contains($range3));
        self::assertFalse($range2->contains($range3));
        self::assertTrue($range3->contains($range3)); // self
        self::assertTrue($range4->contains($range3));

        // range 4
        self::assertFalse($range1->contains($range4));
        self::assertFalse($range2->contains($range4));
        self::assertFalse($range3->contains($range4));
        self::assertTrue($range4->contains($range4)); // self
    }

    public function testV6(): void
    {
        $ip1 = IPv6Address::fromString('2a03:2880:f113:81:face:b00c:0:25de');
        $ip2 = IPv6Address::fromString('2a00:1450:4026:808::200e');

        $range1 = IPv6Range::fromString('2a00:1450:4000::/37');
        $range2 = IPv6Range::fromString('2a03:2880::/29');
        $range3 = IPv6Range::fromString('2a03::/16');
        $range4 = IPv6Range::fromString('::/0'); // all addresses

        // ip 1
        self::assertFalse($range1->contains($ip1));
        self::assertTrue($range2->contains($ip1));
        self::assertTrue($range3->contains($ip1));
        self::assertTrue($range4->contains($ip1));

        // ip 2
        self::assertTrue($range1->contains($ip2));
        self::assertFalse($range2->contains($ip2));
        self::assertFalse($range3->contains($ip2));
        self::assertTrue($range4->contains($ip2));

        // range 1
        self::assertTrue($range1->contains($range1)); // self
        self::assertFalse($range2->contains($range1));
        self::assertFalse($range3->contains($range1));
        self::assertTrue($range4->contains($range1));

        // range 2
        self::assertFalse($range1->contains($range2));
        self::assertTrue($range2->contains($range2)); // self
        self::assertTrue($range3->contains($range2));
        self::assertTrue($range4->contains($range2));

        // range 3
        self::assertFalse($range1->contains($range3));
        self::assertFalse($range2->contains($range3));
        self::assertTrue($range3->contains($range3)); // self
        self::assertTrue($range4->contains($range3));

        // range 4
        self::assertFalse($range1->contains($range4));
        self::assertFalse($range2->contains($range4));
        self::assertFalse($range3->contains($range4));
        self::assertTrue($range4->contains($range4)); // self
    }

    public function testNonStrict(): void
    {
        $ip4 = IPv4Address::fromString('192.168.1.100');
        $ip6 = IPv6Address::fromString('2a00:1450:4026:808::200e');

        $range4 = IPv4Range::fromString('0.0.0.0/0'); // all addresses
        $range6 = IPv6Range::fromString('::/0'); // all addresses

        self::assertFalse($range4->contains($ip6));
        self::assertFalse($range6->contains($ip4));

        self::assertFalse($range4->contains($range6));
        self::assertFalse($range6->contains($range4));
    }

    public function testStrictV4Address(): void
    {
        $range4 = IPv4Range::fromString('0.0.0.0/0');
        $ip6 = IPv6Address::fromString('2a00:1450:4026:808::200e');

        $this->expectException(TypeError::class);

        $range4->contains($ip6, true);
    }

    public function testStrictV4Range(): void
    {
        $range4 = IPv4Range::fromString('0.0.0.0/0');
        $range6 = IPv6Range::fromString('::/0');

        $this->expectException(TypeError::class);

        $range4->contains($range6, true);
    }

    public function testStrictV6Address(): void
    {
        $range6 = IPv6Range::fromString('::/0');
        $ip4 = IPv4Address::fromString('192.168.1.100');

        $this->expectException(TypeError::class);

        $range6->contains($ip4, true);
    }

    public function testStrictV6Range(): void
    {
        $range6 = IPv6Range::fromString('::/0');
        $range4 = IPv4Range::fromString('0.0.0.0/0');

        $this->expectException(TypeError::class);

        $range6->contains($range4, true);
    }
}
