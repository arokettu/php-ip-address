<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class RangeFromStringNonStrictTest extends TestCase
{
    public function testV4NonStrict(): void
    {
        // normal creation
        $range1 = IPv4Block::fromString("127.0.0.0/8");
        self::assertEquals('127.0.0.0/8', $range1->toString());

        $range2 = IPv4Block::fromString("127.0.0.0", 8);
        self::assertEquals('127.0.0.0/8', $range2->toString());

        // prefix present in both fields, int value is ignored
        $range3 = IPv4Block::fromString("127.0.0.0/8", 16);
        self::assertEquals('127.0.0.0/8', $range3->toString());

        // negative prefix
        $range4 = IPv4Block::fromString("127.0.0.0", -25);
        self::assertEquals('127.0.0.0/8', $range4->toString());
        // mostly useful for single ip ranges
        $range5 = IPv4Block::fromString("127.0.0.1", -1);
        self::assertEquals('127.0.0.1/32', $range5->toString());

        // auto normalization
        $range6 = IPv4Block::fromString("127.0.0.1/8");
        self::assertEquals('127.0.0.0/8', $range6->toString());
    }

    public function testV4NegativeInString(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Prefix value "-1" appears to be invalid');

        IPv4Block::fromString("127.0.0.1/-1"); // nope
    }

    public function testV6NonStrict(): void
    {
        // normal creation
        $range1 = IPv6Block::fromString("fe80::/10");
        self::assertEquals('fe80::/10', $range1->toString());

        $range2 = IPv6Block::fromString("fe80::", 10);
        self::assertEquals('fe80::/10', $range2->toString());

        // prefix present in both fields, int value is ignored
        $range3 = IPv6Block::fromString("fe80::/10", 16);
        self::assertEquals('fe80::/10', $range3->toString());

        // negative prefix
        $range6 = IPv6Block::fromString("fe80::", -119);
        self::assertEquals('fe80::/10', $range6->toString());
        // mostly useful for single ip ranges
        $range5 = IPv6Block::fromString("fe80::1", -1);
        self::assertEquals('fe80::1/128', $range5->toString());

        // auto normalization
        $range6 = IPv6Block::fromString("fe80::1/10");
        self::assertEquals('fe80::/10', $range6->toString());
    }

    public function testV6NegativeInString(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Prefix value "-1" appears to be invalid');

        IPv6Block::fromString("fe80::1/-1"); // nope
    }

    public function testAutoDetection(): void
    {
        $range4 = IPBlock::fromString("127.0.0.0/8");
        self::assertInstanceOf(IPv4Block::class, $range4);
        self::assertEquals('127.0.0.0/8', $range4->toString());

        $range6 = IPBlock::fromString("fe80::/10");
        self::assertInstanceOf(IPv6Block::class, $range6);
        self::assertEquals('fe80::/10', $range6->toString());
    }

    public function testAutoDetectionFailed(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'IP range was not recognized: ' .
            '"Base address "127.15.365.5" does not appear to be a valid IPv4 address", ' .
            '"Base address "127.15.365.5" does not appear to be a valid IPv6 address"'
        );

        IPBlock::fromString("127.15.365.5/8");
    }
}
