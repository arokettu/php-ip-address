<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Block;
use PHPUnit\Framework\TestCase;
use TypeError;

class BlockContainsTest extends TestCase
{
    public function testV4(): void
    {
        $ip1 = IPv4Address::fromString('192.168.1.100');
        $ip2 = IPv4Address::fromString('127.0.0.1');

        $block1 = IPv4Block::fromString('127.0.0.0/8');
        $block2 = IPv4Block::fromString('192.168.1.0/24');
        $block3 = IPv4Block::fromString('192.168.0.0/16');
        $block4 = IPv4Block::fromString('0.0.0.0/0'); // all addresses

        // ip 1
        self::assertFalse($block1->contains($ip1));
        self::assertTrue($block2->contains($ip1));
        self::assertTrue($block3->contains($ip1));
        self::assertTrue($block4->contains($ip1));

        // ip 2
        self::assertTrue($block1->contains($ip2));
        self::assertFalse($block2->contains($ip2));
        self::assertFalse($block3->contains($ip2));
        self::assertTrue($block4->contains($ip2));

        // block 1
        self::assertTrue($block1->contains($block1)); // self
        self::assertFalse($block2->contains($block1));
        self::assertFalse($block3->contains($block1));
        self::assertTrue($block4->contains($block1));

        // block 2
        self::assertFalse($block1->contains($block2));
        self::assertTrue($block2->contains($block2)); // self
        self::assertTrue($block3->contains($block2));
        self::assertTrue($block4->contains($block2));

        // block 3
        self::assertFalse($block1->contains($block3));
        self::assertFalse($block2->contains($block3));
        self::assertTrue($block3->contains($block3)); // self
        self::assertTrue($block4->contains($block3));

        // block 4
        self::assertFalse($block1->contains($block4));
        self::assertFalse($block2->contains($block4));
        self::assertFalse($block3->contains($block4));
        self::assertTrue($block4->contains($block4)); // self
    }

    public function testV6(): void
    {
        $ip1 = IPv6Address::fromString('2a03:2880:f113:81:face:b00c:0:25de');
        $ip2 = IPv6Address::fromString('2a00:1450:4026:808::200e');

        $block1 = IPv6Block::fromString('2a00:1450:4000::/37');
        $block2 = IPv6Block::fromString('2a03:2880::/29');
        $block3 = IPv6Block::fromString('2a03::/16');
        $block4 = IPv6Block::fromString('::/0'); // all addresses

        // ip 1
        self::assertFalse($block1->contains($ip1));
        self::assertTrue($block2->contains($ip1));
        self::assertTrue($block3->contains($ip1));
        self::assertTrue($block4->contains($ip1));

        // ip 2
        self::assertTrue($block1->contains($ip2));
        self::assertFalse($block2->contains($ip2));
        self::assertFalse($block3->contains($ip2));
        self::assertTrue($block4->contains($ip2));

        // block 1
        self::assertTrue($block1->contains($block1)); // self
        self::assertFalse($block2->contains($block1));
        self::assertFalse($block3->contains($block1));
        self::assertTrue($block4->contains($block1));

        // block 2
        self::assertFalse($block1->contains($block2));
        self::assertTrue($block2->contains($block2)); // self
        self::assertTrue($block3->contains($block2));
        self::assertTrue($block4->contains($block2));

        // block 3
        self::assertFalse($block1->contains($block3));
        self::assertFalse($block2->contains($block3));
        self::assertTrue($block3->contains($block3)); // self
        self::assertTrue($block4->contains($block3));

        // block 4
        self::assertFalse($block1->contains($block4));
        self::assertFalse($block2->contains($block4));
        self::assertFalse($block3->contains($block4));
        self::assertTrue($block4->contains($block4)); // self
    }

    public function testNonStrict(): void
    {
        $ip4 = IPv4Address::fromString('192.168.1.100');
        $ip6 = IPv6Address::fromString('2a00:1450:4026:808::200e');

        $block4 = IPv4Block::fromString('0.0.0.0/0'); // all addresses
        $block6 = IPv6Block::fromString('::/0'); // all addresses

        self::assertFalse($block4->contains($ip6));
        self::assertFalse($block6->contains($ip4));

        self::assertFalse($block4->contains($block6));
        self::assertFalse($block6->contains($block4));
    }

    public function testStrictV4Address(): void
    {
        $block4 = IPv4Block::fromString('0.0.0.0/0');
        $ip6 = IPv6Address::fromString('2a00:1450:4026:808::200e');

        $this->expectException(TypeError::class);

        $block4->contains($ip6, true);
    }

    public function testStrictV4Block(): void
    {
        $block4 = IPv4Block::fromString('0.0.0.0/0');
        $block6 = IPv6Block::fromString('::/0');

        $this->expectException(TypeError::class);

        $block4->contains($block6, true);
    }

    public function testStrictV6Address(): void
    {
        $block6 = IPv6Block::fromString('::/0');
        $ip4 = IPv4Address::fromString('192.168.1.100');

        $this->expectException(TypeError::class);

        $block6->contains($ip4, true);
    }

    public function testStrictV6Block(): void
    {
        $block6 = IPv6Block::fromString('::/0');
        $block4 = IPv4Block::fromString('0.0.0.0/0');

        $this->expectException(TypeError::class);

        $block6->contains($block4, true);
    }
}
