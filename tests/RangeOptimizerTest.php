<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Range;
use Arokettu\IP\Tools\RangeOptimizer;
use PHPUnit\Framework\TestCase;

class RangeOptimizerTest extends TestCase
{
    public function testIPv4(): void
    {
        $ranges = [
            // duplicates
            '1.2.0.0/16',
            '1.2.0.0/16',
            '1.2.0.0/16',
            // wider range
            '4.3.2.1/32',
            '4.3.0.0/24',
            '4.3.0.0/16',
            '4.3.0.0/20',
            // collapsable range
            '23.42.0.0/17',
            '23.42.128.0/17',
            // cascade collapsable range
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

        shuffle($ranges);

        // prepare data
        $ranges = array_map(
            fn ($s) => IPv4Range::fromString($s, -1),
            $ranges,
        );

        $optimized = RangeOptimizer::optimizeV4(...$ranges);

        $optimized = array_map(fn ($r) => $r->toString(), $optimized);

        self::assertEquals($optimizedExpected, $optimized);
    }

    public function testIPv6(): void
    {
        $ranges = [
            // duplicates
            '1111:2222::/32',
            '1111:2222::/32',
            '1111:2222::/32',
            // wider range
            '4444:3333:2222:1111::/64',
            '4444:3333::/122',
            '4444:3333::/32',
            '4444:3333::/96',
            // collapsable range
            '2323:4242:0::/33',
            '2323:4242:8000::/33',
            // cascade collapsable range
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

        shuffle($ranges);

        // prepare data
        $ranges = array_map(
            fn ($s) => IPv6Range::fromString($s, -1),
            $ranges,
        );

        $optimized = RangeOptimizer::optimizeV6(...$ranges);

        $optimized = array_map(fn ($r) => $r->toString(), $optimized);

        self::assertEquals($optimizedExpected, $optimized);
    }
}