<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPAddress;
use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use DomainException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class AddressFactoriesTest extends TestCase
{
    public function testV4(): void
    {
        $ip1 = new IPv4Address("\x7f\x00\x00\x01");
        self::assertEquals('127.0.0.1', $ip1->toString());

        $ip2 = IPv4Address::fromBytes('abcd');
        self::assertEquals('97.98.99.100', $ip2->toString());

        $ip3 = IPv4Address::fromString('192.168.1.100');
        self::assertEquals('192.168.1.100', $ip3->toString());
    }

    public function testIPv4WrongLength(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPv4 address must be exactly 4 bytes');

        new IPv4Address('abcdabcdabcdabcd');
    }

    public function testIPv4WrongLengthFromBytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv4 address must be exactly 4 bytes');

        IPv4Address::fromBytes('abcdabcdabcdabcd');
    }

    public function testIPv4WrongFormat(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('$string must be a valid IPv4 address, "abcd::abcd" given');

        IPv4Address::fromString('abcd::abcd');
    }

    public function testV6(): void
    {
        $ip1 = new IPv6Address("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\1");
        self::assertEquals('::1', $ip1->toString());

        $ip2 = IPv6Address::fromBytes('abcdabcdabcdabcd');
        self::assertEquals('6162:6364:6162:6364:6162:6364:6162:6364', $ip2->toString());

        $ip3 = IPv6Address::fromString('2A00:1450:4026:0808:0:0:0:200E');
        self::assertEquals('2a00:1450:4026:808::200e', $ip3->toString());

        // special normalization for IPv4 mapping
        $ip4 = IPv6Address::fromString('::ffff:c0a8:0164');
        self::assertEquals('::ffff:192.168.1.100', $ip4->toString());

        // the other way around
        $ip4 = IPv6Address::fromString('fc00::192.168.1.100');
        self::assertEquals('fc00::c0a8:164', $ip4->toString());
    }

    public function testIPv6WrongLength(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPv6 address must be exactly 16 bytes');

        new IPv6Address('abcd');
    }

    public function testIPv6WrongLengthFromBytes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IPv6 address must be exactly 16 bytes');

        IPv6Address::fromBytes('abcd');
    }

    public function testIPv6WrongFormat(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('$string must be a valid IPv6 address, "192.168.1.100" given');

        IPv6Address::fromString('192.168.1.100');
    }

    public function testAutodetect(): void
    {
        $ip1 = IPAddress::fromBytes('abcd');
        self::assertInstanceOf(IPv4Address::class, $ip1);
        self::assertEquals('97.98.99.100', $ip1->toString());

        $ip2 = IPAddress::fromBytes('abcdabcdabcdabcd');
        self::assertInstanceOf(IPv6Address::class, $ip2);
        self::assertEquals('6162:6364:6162:6364:6162:6364:6162:6364', $ip2->toString());

        $ip3 = IPAddress::fromString('192.168.1.100');
        self::assertInstanceOf(IPv4Address::class, $ip3);
        self::assertEquals('192.168.1.100', $ip3->toString());

        $ip4 = IPAddress::fromString('abcd::abcd');
        self::assertInstanceOf(IPv6Address::class, $ip4);
        self::assertEquals('abcd::abcd', $ip4->toString());
    }

    public function testAutodetectFailed(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('IP address was not recognized, 8 is not a valid byte length');

        IPAddress::fromBytes('abcdabcd');
    }

    public function testAutodetectWrongFormat(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('$string must be a valid IP address, "something that is not an ip" given');

        IPAddress::fromString('something that is not an ip');
    }
}
