<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use DomainException;
use PHPUnit\Framework\TestCase;

class AddressMiscGettersTest extends TestCase
{
    public function testBytes(): void
    {
        $ip4 = new IPv4Address('abcd');
        $ip6 = new IPv6Address('abcdabcdabcdabcd');

        self::assertEquals('abcd', $ip4->getBytes());
        self::assertEquals('abcdabcdabcdabcd', $ip6->getBytes());
    }

    public function testSingleIPBlock(): void
    {
        $ip4 = IPv4Address::fromString('127.0.0.1');
        $ip6 = IPv6Address::fromString('::1');

        self::assertEquals('127.0.0.1/32', (string)$ip4->toBlock());
        self::assertEquals('::1/128', (string)$ip6->toBlock());
    }

    public function testLargerBlock(): void
    {
        $ip4 = new IPv4Address('abcd');
        $ip6 = new IPv6Address('abcdabcdabcdabcd');

        self::assertEquals('97.0.0.0/8', (string)$ip4->toBlock(8));
        self::assertEquals('6162:6364::/32', (string)$ip6->toBlock(32));
    }

    public function testIPv6Conversion(): void
    {
        $ip = IPv4Address::fromString('64.92.175.4');

        self::assertEquals('::ffff:64.92.175.4', (string)$ip->toMappedIPv6());
        self::assertEquals('::64.92.175.4', (string)$ip->toCompatibleIPv6());
    }

    public function testIPv4EncodedInIPv6(): void
    {
        $ipMapped = IPv6Address::fromString('::ffff:64.92.175.4');
        $ipCompat = IPv6Address::fromString('::64.92.175.4');
        $ipNotV4  = IPv6Address::fromString('2001::64.92.175.4');

        self::assertTrue($ipMapped->isMappedIPv4());
        self::assertFalse($ipCompat->isMappedIPv4());
        self::assertFalse($ipNotV4->isMappedIPv4());

        self::assertFalse($ipMapped->isCompatibleIPv4());
        self::assertTrue($ipCompat->isCompatibleIPv4());
        self::assertFalse($ipNotV4->isCompatibleIPv4());

        self::assertTrue($ipMapped->isIPv4());
        self::assertFalse($ipCompat->isIPv4());
        self::assertFalse($ipNotV4->isIPv4());

        self::assertEquals('64.92.175.4', (string)$ipMapped->getIPv4());
        self::assertEquals('64.92.175.4', (string)$ipCompat->getIPv4(true));
    }

    public function testIPv4CompatibleIsNotIPv4ByDefault(): void
    {
        $ipCompat = IPv6Address::fromString('::64.92.175.4');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('This IPv6 address does not encode IPv4');

        self::assertEquals('64.92.175.4', (string)$ipCompat->getIPv4());
    }

    public function testLocalhostIsNotIPv4(): void
    {
        $localhost = IPv6Address::fromString('::1');

        self::assertFalse($localhost->isMappedIPv4());
        self::assertFalse($localhost->isCompatibleIPv4());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('This IPv6 address does not encode IPv4');

        $localhost->getIPv4();
    }

    public function testZeroIsNotIPv4(): void
    {
        $localhost = IPv6Address::fromString('::');

        self::assertFalse($localhost->isMappedIPv4());
        self::assertFalse($localhost->isCompatibleIPv4());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('This IPv6 address does not encode IPv4');

        $localhost->getIPv4();
    }

    public function testIPv4NotEncodedInIPv6(): void
    {
        $ipNotV4 = IPv6Address::fromString('2001::64.92.175.4');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('This IPv6 address does not encode IPv4');

        $ipNotV4->getIPv4();
    }

    public function testFullHex(): void
    {
        $lh = IPv6Address::fromString('::1');
        $ip = IPv6Address::fromString('2001::64.92.175.4');

        self::assertEquals('0000:0000:0000:0000:0000:0000:0000:0001', $lh->toFullHexString());
        self::assertEquals('2001:0000:0000:0000:0000:0000:405c:af04', $ip->toFullHexString());
    }
}
