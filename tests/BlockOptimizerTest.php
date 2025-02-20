<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPBlock;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;
use Arokettu\IP\Tools\BlockOptimizer;
use PHPUnit\Framework\TestCase;

class BlockOptimizerTest extends TestCase
{
    public function testIPv4(): void
    {
        $blocks = [
            // duplicates
            '1.2.0.0/16',
            '1.2.0.0/16',
            '1.2.0.0/16',
            // wider block
            '4.3.2.1/32',
            '4.3.0.0/24',
            '4.3.0.0/16',
            '4.3.0.0/20',
            // collapsable block
            '23.42.0.0/17',
            '23.42.128.0/17',
            // cascade collapsable block
            '42.23.192.0/18',
            '42.23.0.0/17',
            '42.23.128.0/18',
            // not collapsable
            '12.13.2.0/28',
            '12.13.3.0/28',
            // ip collapsible
            '34.15.96.8',
            '34.15.96.9',
            '34.15.96.10',
            '34.15.96.11',
            // ip non-collapsible
            '92.44.75.67',
            '92.44.75.68',
        ];

        $optimizedExpected = [
            '1.2.0.0/16',
            '4.3.0.0/16',
            '12.13.2.0/28',
            '12.13.3.0/28',
            '23.42.0.0/16',
            '34.15.96.8/30',
            '42.23.0.0/16',
            '92.44.75.67/32',
            '92.44.75.68/32',
        ];

        shuffle($blocks);

        // prepare data
        $blocks = array_map(
            fn ($s) => IPv4Block::fromString($s, -1),
            $blocks,
        );

        $optimized = BlockOptimizer::optimizeV4(...$blocks);

        $optimized = array_map(fn ($r) => $r->toString(), $optimized);

        self::assertEquals($optimizedExpected, $optimized);
    }

    public function testIPv6(): void
    {
        $blocks = [
            // duplicates
            '1111:2222::/32',
            '1111:2222::/32',
            '1111:2222::/32',
            // wider block
            '4444:3333:2222:1111::/64',
            '4444:3333::/122',
            '4444:3333::/32',
            '4444:3333::/96',
            // collapsable block
            '2323:4242:0::/33',
            '2323:4242:8000::/33',
            // cascade collapsable block
            '4242:2323:c000::/34',
            '4242:2323:0::/33',
            '4242:2323:8000::/34',
            // not collapsable
            '1212:1313:0002::/64',
            '1212:1313:0003::/64',
            // ip collapsible
            '5698:abcd::0120',
            '5698:abcd::0121',
            '5698:abcd::0122',
            '5698:abcd::0123',
            // ip non-collapsible
            'abcd:4321::abd7',
            'abcd:4321::abd8',
        ];

        $optimizedExpected = [
            '1111:2222::/32',
            '1212:1313:2::/64',
            '1212:1313:3::/64',
            '2323:4242::/32',
            '4242:2323::/32',
            '4444:3333::/32',
            '5698:abcd::120/126',
            'abcd:4321::abd7/128',
            'abcd:4321::abd8/128',
        ];

        shuffle($blocks);

        // prepare data
        $blocks = array_map(
            fn ($s) => IPv6Block::fromString($s, -1),
            $blocks,
        );

        $optimized = BlockOptimizer::optimizeV6(...$blocks);

        $optimized = array_map(fn ($r) => $r->toString(), $optimized);

        self::assertEquals($optimizedExpected, $optimized);
    }

    public function testCombined(): void
    {
        $blocks = [
            // duplicates
            '1.2.0.0/16',
            '1.2.0.0/16',
            '1.2.0.0/16',
            // wider block
            '4.3.2.1/32',
            '4.3.0.0/24',
            '4.3.0.0/16',
            '4.3.0.0/20',
            // collapsable block
            '23.42.0.0/17',
            '23.42.128.0/17',
            // cascade collapsable block
            '42.23.192.0/18',
            '42.23.0.0/17',
            '42.23.128.0/18',
            // not collapsable
            '12.13.2.0/28',
            '12.13.3.0/28',
            // ip collapsible
            '34.15.96.8',
            '34.15.96.9',
            '34.15.96.10',
            '34.15.96.11',
            // ip non-collapsible
            '92.44.75.67',
            '92.44.75.68',
            // duplicates
            '1111:2222::/32',
            '1111:2222::/32',
            '1111:2222::/32',
            // wider block
            '4444:3333:2222:1111::/64',
            '4444:3333::/122',
            '4444:3333::/32',
            '4444:3333::/96',
            // collapsable block
            '2323:4242:0::/33',
            '2323:4242:8000::/33',
            // cascade collapsable block
            '4242:2323:c000::/34',
            '4242:2323:0::/33',
            '4242:2323:8000::/34',
            // not collapsable
            '1212:1313:0002::/64',
            '1212:1313:0003::/64',
            // ip collapsible
            '5698:abcd::0120',
            '5698:abcd::0121',
            '5698:abcd::0122',
            '5698:abcd::0123',
            // ip non-collapsible
            'abcd:4321::abd7',
            'abcd:4321::abd8',
        ];

        $optimizedExpected = [
            '1.2.0.0/16',
            '4.3.0.0/16',
            '12.13.2.0/28',
            '12.13.3.0/28',
            '23.42.0.0/16',
            '34.15.96.8/30',
            '42.23.0.0/16',
            '92.44.75.67/32',
            '92.44.75.68/32',
            '1111:2222::/32',
            '1212:1313:2::/64',
            '1212:1313:3::/64',
            '2323:4242::/32',
            '4242:2323::/32',
            '4444:3333::/32',
            '5698:abcd::120/126',
            'abcd:4321::abd7/128',
            'abcd:4321::abd8/128',
        ];

        shuffle($blocks);

        // prepare data
        $blocks = array_map(
            fn ($s) => IPBlock::fromString($s, -1),
            $blocks,
        );

        $optimized = BlockOptimizer::optimize(...$blocks);

        $optimized = array_map(fn ($r) => $r->toString(), $optimized);

        self::assertEquals($optimizedExpected, $optimized);
    }

    public function testCodeEdgeCases(): void
    {
        $block1 = IPv4Block::fromString('127.0.0.0/8');
        $block2 = IPv4Block::fromString('127.0.0.0/16');
        $block3 = IPv4Block::fromString('127.0.5.0/24');
        $block4 = IPv4Block::fromString('127.1.0.0/16');
        $block5 = IPv4Block::fromString('127.0.0.0/15');

        // optimize zero
        self::assertEquals([], BlockOptimizer::optimizeV4());

        // optimize one
        $one1 = [$block1];
        self::assertEquals($one1, BlockOptimizer::optimizeV4(...$one1));

        // after the optimization only one is left
        self::assertEquals($one1, BlockOptimizer::optimizeV4($block1, $block2, $block3));

        // after gluing only one is left
        $one2 = [$block5];
        self::assertEquals($one2, BlockOptimizer::optimizeV4($block2, $block3, $block4));
    }

    public function testMergeDown(): void
    {
        $block1 = IPv6Block::fromString('2001:0000::/32', strict: true);
        $block2 = IPv6Block::fromString('2001:0001::/32', strict: true);
        $block3 = IPv6Block::fromString('2001:0002::/31', strict: true);
        $block4 = IPv6Block::fromString('2001:0004::/30', strict: true);
        $block5 = IPv6Block::fromString('2001:0008::/29', strict: true);

        $result = IPv6Block::fromString('2001:0000::/28', strict: true);

        self::assertEquals([$result], BlockOptimizer::optimizeV6($block1, $block2, $block3, $block4, $block5));
    }

    public function testMergeUp(): void
    {
        $block1 = IPv6Block::fromString('2001:0000::/29', strict: true);
        $block2 = IPv6Block::fromString('2001:0008::/30', strict: true);
        $block3 = IPv6Block::fromString('2001:000c::/31', strict: true);
        $block4 = IPv6Block::fromString('2001:000e::/32', strict: true);
        $block5 = IPv6Block::fromString('2001:000f::/32', strict: true);

        $result = IPv6Block::fromString('2001:0000::/28', strict: true);

        self::assertEquals([$result], BlockOptimizer::optimizeV6($block1, $block2, $block3, $block4, $block5));
    }
}
