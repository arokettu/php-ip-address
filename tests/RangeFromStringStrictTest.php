<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class RangeFromStringStrictTest extends TestCase
{
    public function testV4FromString(): void
    {
        $range1 = IPv4Block::fromString("127.0.0.0/8", null, true);
        self::assertEquals('127.0.0.0/8', $range1->toString());

        $range2 = IPv4Block::fromString("127.0.0.0", 8, true);
        self::assertEquals('127.0.0.0/8', $range2->toString());

        self::assertEquals($range1, $range2);
    }

    public function testV4InvalidFormat(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address "fe80::" does not appear to be a valid IPv4 address');

        IPv4Block::fromString("fe80::/10", null, true);
    }

    public function testV4Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 prefix must be in range 0-32');

        IPv4Block::fromString("127.0.0.0", 64, true);
    }

    public function testV4Normalized(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 range is not in a normalized form');

        IPv4Block::fromString("127.0.0.1/8", null, true);
    }

    public function testV4DoublePrefix(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('In strict mode prefix cannot appear in both string and $mask param');

        IPv4Block::fromString("127.0.0.0/8", 8, true);
    }

    public function testV4BrokenPrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Prefix value "ab" appears to be invalid');

        IPv4Block::fromString("127.0.0.0/ab", null, true);
    }

    public function testV6FromString(): void
    {
        $range1 = IPv6Block::fromString("fe80::/10", null, true);
        self::assertEquals('fe80::/10', $range1->toString());

        $range2 = IPv6Block::fromString("fe80::", 10, true);
        self::assertEquals('fe80::/10', $range2->toString());

        self::assertEquals($range1, $range2);
    }

    public function testV6InvalidFormat(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address "127.0.0.0" does not appear to be a valid IPv6 address');

        IPv6Block::fromString("127.0.0.0/8", null, true);
    }

    public function testV6Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 prefix must be in range 0-128');

        IPv6Block::fromString("fe80::/300", null, true);
    }

    public function testV6Normalized(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 range is not in a normalized form');

        IPv6Block::fromString("fe80::1/10", null, true);
    }

    public function testV6DoublePrefix(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('In strict mode prefix cannot appear in both string and $mask param');

        IPv6Block::fromString("fe80::/10", 10, true);
    }

    public function testV6BrokenPrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Prefix value "ab" appears to be invalid');

        IPv4Block::fromString("fe80::/ab", null, true);
    }

    public function testAutoDetection(): void
    {
        $range4 = IPBlock::fromString("127.0.0.0/8", strict: true);
        self::assertInstanceOf(IPv4Block::class, $range4);
        self::assertEquals('127.0.0.0/8', $range4->toString());

        $range6 = IPBlock::fromString("fe80::/10", strict: true);
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

        IPBlock::fromString("127.15.365.5", 8, true);
    }
}
