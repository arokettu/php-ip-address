<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\Tools\CompareHelper;
use PHPUnit\Framework\TestCase;
use TypeError;

class AddressCompareTest extends TestCase
{
    public function testV4Compare(): void
    {
        $ip1 = IPv4Address::fromString('104.244.42.193');
        $ip2 = IPv4Address::fromString('157.240.205.35');
        $ip3 = IPv4Address::fromString('216.58.211.238');

        $sorted = [$ip1, $ip2, $ip3];
        $unsorted = $sorted;
        shuffle($unsorted);

        // non-strict
        self::assertEquals(-1, $ip1->compare($ip2));
        self::assertEquals(0, $ip2->compare($ip2));
        self::assertEquals(1, $ip3->compare($ip2));

        self::assertTrue($ip2->equals($ip2));
        self::assertFalse($ip1->equals($ip2));

        $toBeSorted = $unsorted;
        usort($toBeSorted, fn ($a, $b) => CompareHelper::compare($a, $b));
        self::assertEquals($sorted, $toBeSorted);
        $toBeSorted = $unsorted;
        usort($toBeSorted, CompareHelper::nonStrictCompare(...));
        self::assertEquals($sorted, $toBeSorted);

        // strict
        self::assertEquals(-1, $ip1->compare($ip2, true));
        self::assertEquals(0, $ip2->compare($ip2, true));
        self::assertEquals(1, $ip3->compare($ip2, true));

        self::assertTrue($ip2->equals($ip2, true));
        self::assertFalse($ip1->equals($ip2, true));

        $toBeSorted = $unsorted;
        usort($toBeSorted, fn ($a, $b) => CompareHelper::compare($a, $b, true));
        self::assertEquals($sorted, $toBeSorted);
        $toBeSorted = $unsorted;
        usort($toBeSorted, CompareHelper::strictCompare(...));
        self::assertEquals($sorted, $toBeSorted);
    }

    public function testV6Compare(): void
    {
        $ip1 = IPv6Address::fromString('2606:4700::6810:84e5');
        $ip2 = IPv6Address::fromString('2a00:1450:4026:808::200e');
        $ip3 = IPv6Address::fromString('2a03:2880:f113:81:face:b00c:0:25de');

        $sorted = [$ip1, $ip2, $ip3];
        $unsorted = $sorted;
        shuffle($unsorted);

        // non-strict
        self::assertEquals(-1, $ip1->compare($ip2));
        self::assertEquals(0, $ip2->compare($ip2));
        self::assertEquals(1, $ip3->compare($ip2));

        self::assertTrue($ip2->equals($ip2));
        self::assertFalse($ip1->equals($ip2));

        $toBeSorted = $unsorted;
        usort($toBeSorted, fn ($a, $b) => CompareHelper::compare($a, $b));
        self::assertEquals($sorted, $toBeSorted);
        $toBeSorted = $unsorted;
        usort($toBeSorted, CompareHelper::nonStrictCompare(...));
        self::assertEquals($sorted, $toBeSorted);

        // strict
        self::assertEquals(-1, $ip1->compare($ip2, true));
        self::assertEquals(0, $ip2->compare($ip2, true));
        self::assertEquals(1, $ip3->compare($ip2, true));

        self::assertTrue($ip2->equals($ip2, true));
        self::assertFalse($ip1->equals($ip2, true));

        $toBeSorted = $unsorted;
        usort($toBeSorted, fn ($a, $b) => CompareHelper::compare($a, $b, true));
        self::assertEquals($sorted, $toBeSorted);
        $toBeSorted = $unsorted;
        usort($toBeSorted, CompareHelper::strictCompare(...));
        self::assertEquals($sorted, $toBeSorted);
    }

    public function testCrossTypeCompare(): void
    {
        $ip1 = IPv4Address::fromString('104.244.42.193');
        $ip2 = IPv6Address::fromString('2606:4700::6810:84e5');
        $ip3 = IPv4Address::fromString('157.240.205.35');
        $ip4 = IPv6Address::fromString('2a00:1450:4026:808::200e');
        $ip5 = IPv6Address::fromString('::104.244.42.193');
        $ip6 = IPv6Address::fromString('68f4:2ac1::');

        $sorted = [$ip1, $ip3, $ip5, $ip2, $ip4, $ip6];
        $unsorted = $sorted;
        shuffle($unsorted);

        // v4 sorted before v6
        self::assertEquals(-1, $ip1->compare($ip2));
        self::assertEquals(1, $ip2->compare($ip3));

        // even equal value are not equal
        self::assertStringEndsWith($ip1->bytes, $ip5->bytes);
        self::assertStringStartsWith($ip1->bytes, $ip6->bytes);
        self::assertFalse($ip1->equals($ip5));
        self::assertFalse($ip1->equals($ip6));

        // ipv4 are sorted before ipv6
        $toBeSorted = $unsorted;
        usort($toBeSorted, CompareHelper::nonStrictCompare(...));
        self::assertEquals($sorted, $toBeSorted);
    }

    public function testStrictCompareV4(): void
    {
        $ip4 = IPv4Address::fromString('104.244.42.193');
        $ip6 = IPv6Address::fromString('2606:4700::6810:84e5');

        $this->expectException(TypeError::class);
        $ip4->compare($ip6, true);
    }

    public function testStrictCompareV6(): void
    {
        $ip4 = IPv4Address::fromString('104.244.42.193');
        $ip6 = IPv6Address::fromString('2606:4700::6810:84e5');

        $this->expectException(TypeError::class);
        $ip6->compare($ip4, true);
    }

    public function testStrictEqualsV4(): void
    {
        $ip4 = IPv4Address::fromString('104.244.42.193');
        $ip6 = IPv6Address::fromString('2606:4700::6810:84e5');

        $this->expectException(TypeError::class);
        $ip4->equals($ip6, true);
    }

    public function testStrictEqualsV6(): void
    {
        $ip4 = IPv4Address::fromString('104.244.42.193');
        $ip6 = IPv6Address::fromString('2606:4700::6810:84e5');

        $this->expectException(TypeError::class);
        $ip6->equals($ip4, true);
    }
}
