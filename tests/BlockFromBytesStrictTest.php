<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class BlockFromBytesStrictTest extends TestCase
{
    public function testV4FromBytes(): void
    {
        $range = IPv4Block::fromBytes("\x7f\0\0\0", 8, true);
        self::assertEquals('127.0.0.0/8', $range->toString());
    }

    public function testV4From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv4 range must be exactly 4 bytes');

        IPv4Block::fromBytes("abcdef", 8, true);
    }

    public function testV4Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 prefix must be in range 0-32');

        IPv4Block::fromBytes("abcd", 64, true);
    }

    public function testV4Normalized(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 range is not in a normalized form');

        IPv4Block::fromBytes("\x7f\0\0\1", 8, true);
    }

    public function testV6FromBytes(): void
    {
        $range = IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10, true);
        self::assertEquals('fe80::/10', $range->toString());
    }

    public function testV6From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv6 range must be exactly 16 bytes');

        IPv6Block::fromBytes("abcdef", 8, true);
    }

    public function testV6ConstructPrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 prefix must be in range 0-128');

        IPv6Block::fromBytes("abcdabcdabcdabcd", 300, true);
    }

    public function testV6Normalized(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 range is not in a normalized form');

        IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", 10, true);
    }

    public function testAutoDetection(): void
    {
        $range4 = IPBlock::fromBytes("\x7f\0\0\0", 8, true);
        self::assertInstanceOf(IPv4Block::class, $range4);
        self::assertEquals('127.0.0.0/8', $range4->toString());

        $range6 = IPBlock::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10, true);
        self::assertInstanceOf(IPv6Block::class, $range6);
        self::assertEquals('fe80::/10', $range6->toString());
    }

    public function testAutoDetectionFailed(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IP range was not recognized, 6 is not a valid byte length');

        IPBlock::fromBytes("abcdef", 8, true);
    }
}
