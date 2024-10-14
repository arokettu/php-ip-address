<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
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
}
