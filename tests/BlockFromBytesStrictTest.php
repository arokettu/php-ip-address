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
        $block = IPv4Block::fromBytes("\x7f\0\0\0", 8, true);
        self::assertEquals('127.0.0.0/8', $block->toString());
    }

    public function testV4From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv4 block must be exactly 4 bytes');

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
        $this->expectExceptionMessage('IPv4 block is not in a normalized form');

        IPv4Block::fromBytes("\x7f\0\0\1", 8, true);
    }

    public function testV6FromBytes(): void
    {
        $block = IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10, true);
        self::assertEquals('fe80::/10', $block->toString());
    }

    public function testV6From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv6 block must be exactly 16 bytes');

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
        $this->expectExceptionMessage('IPv6 block is not in a normalized form');

        IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", 10, true);
    }

    public function testAutoDetection(): void
    {
        $block4 = IPBlock::fromBytes("\x7f\0\0\0", 8, true);
        self::assertInstanceOf(IPv4Block::class, $block4);
        self::assertEquals('127.0.0.0/8', $block4->toString());

        $block6 = IPBlock::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10, true);
        self::assertInstanceOf(IPv6Block::class, $block6);
        self::assertEquals('fe80::/10', $block6->toString());
    }

    public function testAutoDetectionFailed(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IP block was not recognized, 6 is not a valid byte length');

        IPBlock::fromBytes("abcdef", 8, true);
    }
}
