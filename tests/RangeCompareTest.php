<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Range;
use Arokettu\IP\Tools\CompareHelper;
use PHPUnit\Framework\TestCase;
use TypeError;

class RangeCompareTest extends TestCase
{
    public function testV4Compare(): void
    {
        $ip1 = IPv4Range::fromString('104.244.0.0/16');
        $ip2 = IPv4Range::fromString('157.240.0.0/16');
        $ip3 = IPv4Range::fromString('157.240.0.0/24');

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
        $ip1 = IPv6Range::fromString('2606:4700::/16');
        $ip2 = IPv6Range::fromString('2a00:1450::/16');
        $ip3 = IPv6Range::fromString('2a00:1450::/64');

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
        $ip1 = IPv4Range::fromString('104.244.42.193', -1);
        $ip2 = IPv6Range::fromString('2606:4700::6810:84e5', -1);
        $ip3 = IPv4Range::fromString('157.240.205.35', -1);
        $ip4 = IPv6Range::fromString('2a00:1450:4026:808::200e', -1);
        $ip5 = IPv6Range::fromString('::104.244.42.193/128');
        $ip6 = IPv6Range::fromString('68f4:2ac1::/32');

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
        $ip4 = IPv4Range::fromString('104.244.42.0/24');
        $ip6 = IPv6Range::fromString('2606:4700::/32');

        $this->expectException(TypeError::class);
        $ip4->compare($ip6, true);
    }

    public function testStrictCompareV6(): void
    {
        $ip4 = IPv4Range::fromString('104.244.42.0/24');
        $ip6 = IPv6Range::fromString('2606:4700::/32');

        $this->expectException(TypeError::class);
        $ip6->compare($ip4, true);
    }

    public function testStrictEqualsV4(): void
    {
        $ip4 = IPv4Range::fromString('104.244.42.0/24');
        $ip6 = IPv6Range::fromString('2606:4700::/32');

        $this->expectException(TypeError::class);
        $ip4->equals($ip6, true);
    }

    public function testStrictEqualsV6(): void
    {
        $ip4 = IPv4Range::fromString('104.244.42.0/24');
        $ip6 = IPv6Range::fromString('2606:4700::/32');

        $this->expectException(TypeError::class);
        $ip6->equals($ip4, true);
    }
}
