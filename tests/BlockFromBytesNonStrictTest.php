<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class BlockFromBytesNonStrictTest extends TestCase
{
    public function testV4NonStrictBytes(): void
    {
        // normal creation
        $block1 = IPv4Block::fromBytes("\x7f\0\0\0", 8);
        self::assertEquals('127.0.0.0/8', $block1->toString());

        // negative prefix
        $block2 = IPv4Block::fromBytes("\x7f\0\0\0", -25);
        self::assertEquals('127.0.0.0/8', $block2->toString());
        // mostly useful for single IP blocks
        $block3 = IPv4Block::fromBytes("\x7f\0\0\1", -1);
        self::assertEquals('127.0.0.1/32', $block3->toString());

        // auto normalization
        $block4 = IPv4Block::fromBytes("\x7f\0\0\1", 8);
        self::assertEquals('127.0.0.0/8', $block4->toString());
    }

    public function testV4From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv4 block must be exactly 4 bytes');

        IPv4Block::fromBytes('abcdef', 8);
    }

    public function testV4Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 prefix must be in range 0-32');

        IPv4Block::fromBytes('abcd', 64);
    }

    public function testV4NegativePrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Negative prefix for the IPv4 block must be greater than or equal to -32');

        IPv4Block::fromBytes('abcd', -64);
    }

    public function testV6NonStrictBytes(): void
    {
        // normal creation
        $block1 = IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10);
        self::assertEquals('fe80::/10', $block1->toString());

        // negative prefix
        $block2 = IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", -119);
        self::assertEquals('fe80::/10', $block2->toString());
        // mostly useful for single IP blocks
        $block3 = IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", -1);
        self::assertEquals('fe80::1/128', $block3->toString());

        // auto normalization
        $block4 = IPv6Block::fromBytes("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", 10);
        self::assertEquals('fe80::/10', $block4->toString());
    }

    public function testV6From6Bytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Base address for the IPv6 block must be exactly 16 bytes');

        IPv6Block::fromBytes('abcdef', 8);
    }

    public function testV6Prefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 prefix must be in range 0-128');

        IPv6Block::fromBytes('abcdabcdabcdabcd', 300);
    }

    public function testV6NegativePrefix(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Negative prefix for the IPv6 block must be greater than or equal to -128');

        IPv6Block::fromBytes('abcdabcdabcdabcd', -300);
    }

    public function testAutoDetection(): void
    {
        $block4 = IPBlock::fromBytes("\x7f\0\0\0", 8);
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

        IPBlock::fromBytes('abcdef', 8);
    }
}
