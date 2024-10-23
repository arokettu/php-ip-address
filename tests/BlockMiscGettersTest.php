<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use DomainException;
use PHPUnit\Framework\TestCase;

class BlockMiscGettersTest extends TestCase
{
    public function testFirstLast(): void
    {
        $block4 = IPv4Block::fromString('157.240.0.0/16');
        $block6 = IPv6Block::fromString('2a03:2880::/29');

        self::assertEquals('157.240.0.0', (string)$block4->getFirstAddress());
        self::assertEquals('157.240.255.255', (string)$block4->getLastAddress());

        self::assertEquals('2a03:2880::', (string)$block6->getFirstAddress());
        self::assertEquals('2a03:2887:ffff:ffff:ffff:ffff:ffff:ffff', (string)$block6->getLastAddress());
    }

    public function testBaseGetters(): void
    {
        $block4 = IPv4Block::fromString('157.240.0.0/16');
        $block6 = IPv6Block::fromString('2a03:2880::/29');

        self::assertEquals('9df00000', bin2hex($block4->getBytes()));
        self::assertEquals('ffff0000', bin2hex($block4->getMaskBytes()));
        self::assertEquals('255.255.0.0', $block4->getMaskString());
        self::assertEquals(16, $block4->getPrefix());

        self::assertEquals('2a032880000000000000000000000000', bin2hex($block6->getBytes()));
        self::assertEquals('fffffff8000000000000000000000000', bin2hex($block6->getMaskBytes()));
        self::assertEquals('ffff:fff8::', $block6->getMaskString());
        self::assertEquals(29, $block6->getPrefix());
    }

    public function testIPv6Conversion(): void
    {
        $ip = IPv4Block::fromString('64.92.175.0/24');

        self::assertEquals('::ffff:64.92.175.0/120', (string)$ip->toMappedIPv6());
        self::assertEquals('::64.92.175.0/120', (string)$ip->toCompatibleIPv6());
    }

    public function testIPv4EncodedInIPv6(): void
    {
        $ipMapped = IPv6Block::fromString('::ffff:64.92.175.0/120');
        $ipCompat = IPv6Block::fromString('::64.92.175.0/120');
        $ipNotV4  = IPv6Block::fromString('2001::64.92.175.0/120');

        self::assertTrue($ipMapped->isMappedIPv4());
        self::assertFalse($ipCompat->isMappedIPv4());
        self::assertFalse($ipNotV4->isMappedIPv4());

        self::assertFalse($ipMapped->isCompatibleIPv4());
        self::assertTrue($ipCompat->isCompatibleIPv4());
        self::assertFalse($ipNotV4->isCompatibleIPv4());

        self::assertTrue($ipMapped->isIPv4());
        self::assertFalse($ipCompat->isIPv4());
        self::assertFalse($ipNotV4->isIPv4());

        self::assertEquals('64.92.175.0/24', (string)$ipMapped->getIPv4());
    }

    public function testLocalhostIsNotIPv4(): void
    {
        $localhost = IPv6Block::fromString('::1/128');

        self::assertFalse($localhost->isIPv4());
        self::assertTrue($localhost->isCompatibleIPv4()); // this is why compatible range is problematic

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('This IPv6 block does not encode IPv4');

        $localhost->getIPv4();
    }

    public function testIPv4NotEncodedInIPv6(): void
    {
        $ipNotV4 = IPv6Block::fromString('2001::64.92.175.0/120');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('This IPv6 block does not encode IPv4');

        $ipNotV4->getIPv4();
    }

    public function testSingleAddress(): void
    {
        $single4 = IPv4Block::fromString('127.0.0.1', -1);
        $single6 = IPv6Block::fromString('::1', -1);
        $range4  = IPv4Block::fromString('127.0.0.0/8');
        $range6  = IPv6Block::fromString('::/16');

        self::assertTrue($single4->isSingleAddress());
        self::assertTrue($single6->isSingleAddress());
        self::assertFalse($range4->isSingleAddress());
        self::assertFalse($range6->isSingleAddress());
    }
}
