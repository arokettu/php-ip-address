<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\Tools\CompareHelper;
use SlevomatCodingStandard\Sniffs\TestCase;

final class CompareHelperTest extends TestCase
{
    public function testSort(): void
    {
        $values = [
            'a' => IPv4Address::fromString('127.0.5.2'),
            'b' => IPv4Address::fromString('127.0.0.1'),
            'c' => IPv4Address::fromString('127.0.3.54'),
        ];

        $s1 = $values;
        $sorted1 = [$values['b'], $values['c'], $values['a']];
        CompareHelper::sort($s1);
        self::assertEquals($sorted1, $s1);

        $s2 = $values;
        $sorted2 = ['b' => $values['b'], 'c' => $values['c'], 'a' => $values['a']];
        CompareHelper::asort($s2);
        self::assertSame($sorted2, $s2);
        self::assertNotSame($values, $s2);
    }
}
