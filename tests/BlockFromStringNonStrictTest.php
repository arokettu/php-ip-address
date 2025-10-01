<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class BlockFromStringNonStrictTest extends TestCase
{
    public function testV4NonStrict(): void
    {
        // normal creation
        $block1 = IPv4Block::fromString('127.0.0.0/8');
        self::assertEquals('127.0.0.0/8', $block1->toString());

        $block2 = IPv4Block::fromString('127.0.0.0', 8);
        self::assertEquals('127.0.0.0/8', $block2->toString());

        // prefix present in both fields, int value is ignored
        $block3 = IPv4Block::fromString('127.0.0.0/8', 16);
        self::assertEquals('127.0.0.0/8', $block3->toString());

        // negative prefix
        $block4 = IPv4Block::fromString('127.0.0.0', -25);
        self::assertEquals('127.0.0.0/8', $block4->toString());
        // mostly useful for single ip blocks
        $block5 = IPv4Block::fromString('127.0.0.1', -1);
        self::assertEquals('127.0.0.1/32', $block5->toString());

        // auto normalization
        $block6 = IPv4Block::fromString('127.0.0.1/8');
        self::assertEquals('127.0.0.0/8', $block6->toString());
    }

    public function testV4NegativeInString(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Prefix value "-1" appears to be invalid');

        IPv4Block::fromString('127.0.0.1/-1'); // nope
    }

    public function testV6NonStrict(): void
    {
        // normal creation
        $block1 = IPv6Block::fromString('fe80::/10');
        self::assertEquals('fe80::/10', $block1->toString());

        $block2 = IPv6Block::fromString('fe80::', 10);
        self::assertEquals('fe80::/10', $block2->toString());

        // prefix present in both fields, int value is ignored
        $block3 = IPv6Block::fromString('fe80::/10', 16);
        self::assertEquals('fe80::/10', $block3->toString());

        // negative prefix
        $block6 = IPv6Block::fromString('fe80::', -119);
        self::assertEquals('fe80::/10', $block6->toString());
        // mostly useful for single ip blocks
        $block5 = IPv6Block::fromString('fe80::1', -1);
        self::assertEquals('fe80::1/128', $block5->toString());

        // auto normalization
        $block6 = IPv6Block::fromString('fe80::1/10');
        self::assertEquals('fe80::/10', $block6->toString());
    }

    public function testV6NegativeInString(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Prefix value "-1" appears to be invalid');

        IPv6Block::fromString('fe80::1/-1'); // nope
    }

    public function testAutoDetection(): void
    {
        $block4 = IPBlock::fromString('127.0.0.0/8');
        self::assertInstanceOf(IPv4Block::class, $block4);
        self::assertEquals('127.0.0.0/8', $block4->toString());

        $block6 = IPBlock::fromString('fe80::/10');
        self::assertInstanceOf(IPv6Block::class, $block6);
        self::assertEquals('fe80::/10', $block6->toString());
    }

    public function testAutoDetectionFailed(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage(
            'IP block was not recognized: ' .
            '"Base address "127.15.365.5" does not appear to be a valid IPv4 address", ' .
            '"Base address "127.15.365.5" does not appear to be a valid IPv6 address"',
        );

        IPBlock::fromString('127.15.365.5/8');
    }
}
