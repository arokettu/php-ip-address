<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPRange;
use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Range;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class RangeFromBytesNonStrictTest extends TestCase
{
    public function testV4NonStrictBytes(): void
    {
        // normal creation
        $range1 = IPv4Range::fromBytes("\x7f\0\0\0", 8);
        self::assertEquals('127.0.0.0/8', $range1->toString());

        // negative prefix
        $range2 = IPv4Range::fromBytes("\x7f\0\0\0", -25);
        self::assertEquals('127.0.0.0/8', $range2->toString());
        // mostly useful for single IP ranges
        $range3 = IPv4Range::fromBytes("\x7f\0\0\1", -1);
        self::assertEquals('127.0.0.1/32', $range3->toString());

        // auto normalization
        $range4 = IPv4Range::fromBytes("\x7f\0\0\1", 8);
        self::assertEquals('127.0.0.0/8', $range4->toString());
    }

    public function testV4From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv4 range must be exactly 4 bytes');

        IPv4Range::fromBytes("abcdef", 8);
    }

    public function testV4Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 prefix must be in range 0-32');

        IPv4Range::fromBytes("abcd", 64);
    }

    public function testV4NegativePrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Negative prefix for the IPv4 range must be greater than or equal to -32');

        IPv4Range::fromBytes("abcd", -64);
    }

    public function testV6NonStrictBytes(): void
    {
        // normal creation
        $range1 = IPv6Range::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10);
        self::assertEquals('fe80::/10', $range1->toString());

        // negative prefix
        $range2 = IPv6Range::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", -119);
        self::assertEquals('fe80::/10', $range2->toString());
        // mostly useful for single IP ranges
        $range3 = IPv6Range::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", -1);
        self::assertEquals('fe80::1/128', $range3->toString());

        // auto normalization
        $range6 = IPv6Range::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", 10);
        self::assertEquals('fe80::/10', $range6->toString());
    }

    public function testV6From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv6 range must be exactly 16 bytes');

        IPv6Range::fromBytes("abcdef", 8);
    }

    public function testV6Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 prefix must be in range 0-128');

        IPv6Range::fromBytes("abcdabcdabcdabcd", 300);
    }

    public function testV6NegativePrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Negative prefix for the IPv6 range must be greater than or equal to -128');

        IPv6Range::fromBytes("abcdabcdabcdabcd", -300);
    }

    public function testAutoDetection(): void
    {
        $range4 = IPRange::fromBytes("\x7f\0\0\0", 8);
        self::assertInstanceOf(IPv4Range::class, $range4);
        self::assertEquals('127.0.0.0/8', $range4->toString());

        $range6 = IPRange::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10, true);
        self::assertInstanceOf(IPv6Range::class, $range6);
        self::assertEquals('fe80::/10', $range6->toString());
    }

    public function testAutoDetectionFailed(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IP range was not recognized, 6 is not a valid byte length');

        IPRange::fromBytes("abcdef", 8);
    }
}
