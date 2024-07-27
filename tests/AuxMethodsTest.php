<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Range;
use PHPUnit\Framework\TestCase;

class AuxMethodsTest extends TestCase
{
    public function testStringable(): void
    {
        self::assertEquals('216.58.211.238', (string)IPv4Address::fromString('216.58.211.238'));
        self::assertEquals('2a00:1450:4026:808::200e', (string)IPv6Address::fromString('2a00:1450:4026:808::200e'));
        self::assertEquals('216.58.0.0/16', (string)IPv4Range::fromString('216.58.211.238/16'));
        self::assertEquals('2a00:1450:4026:808::/64', (string)IPv6Range::fromString('2a00:1450:4026:808::200e/64'));
    }

    public function testDebugInfo(): void
    {
        self::assertEquals(
            ['value' => '216.58.211.238'],
            IPv4Address::fromString('216.58.211.238')->__debugInfo(),
        );
        self::assertEquals(
            ['value' => '2a00:1450:4026:808::200e'],
            IPv6Address::fromString('2a00:1450:4026:808::200e')->__debugInfo(),
        );
        self::assertEquals(
            ['value' => '216.58.0.0/16'],
            IPv4Range::fromString('216.58.211.238/16')->__debugInfo(),
        );
        self::assertEquals(
            ['value' => '2a00:1450:4026:808::/64'],
            IPv6Range::fromString('2a00:1450:4026:808::200e/64')->__debugInfo(),
        );
    }
}
