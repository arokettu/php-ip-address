<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use DomainException;
use PHPUnit\Framework\TestCase;

class BlockConstructTest extends TestCase
{
    public function testV4Construct(): void
    {
        $range = new IPv4Block("\x7f\0\0\0", 8);
        self::assertEquals('127.0.0.0/8', $range->toString());
    }

    public function testV4Construct6Bytes(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Base address for the IPv4 range must be exactly 4 bytes');

        new IPv4Block("abcdef", 8);
    }

    public function testV4ConstructPrefix(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPv4 prefix must be in range 0-32');

        new IPv4Block("abcd", 64);
    }

    public function testV4Normalized(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPv4 range is not in a normalized form');

        new IPv4Block("\x7f\0\0\1", 8);
    }

    public function testV6Construct(): void
    {
        $range = new IPv6Block("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\0", 10);
        self::assertEquals('fe80::/10', $range->toString());
    }

    public function testV6Construct6Bytes(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Base address for the IPv6 range must be exactly 16 bytes');

        new IPv6Block("abcdef", 8);
    }

    public function testV6ConstructPrefix(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPv6 prefix must be in range 0-128');

        new IPv6Block("abcdabcdabcdabcd", 300);
    }

    public function testV6Normalized(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('IPv6 range is not in a normalized form');

        new IPv6Block("\xfe\x80\0\0\0\0\0\0\0\0\0\0\0\0\0\1", 10);
    }
}
