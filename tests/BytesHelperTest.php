<?php

declare(strict_types=1);

namespace Arokettu\IP\Tests;

use Arokettu\IP\Helpers\BytesHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// we can test full possible ranges here so let's do exactly it
class BytesHelperTest extends TestCase
{
    #[DataProvider('ipv4Masks')]
    public function testIPv4MaskBytes(int $prefix, string $hexmask): void
    {
        self::assertEquals($hexmask, bin2hex(BytesHelper::buildMaskBytes(4, $prefix)));
    }

    #[DataProvider('ipv4Bits')]
    public function testIPv4BitPositions(int $bit, string $hex): void
    {
        self::assertEquals($hex, bin2hex(BytesHelper::buildBitAtPosition(4, $bit)));
    }

    public static function ipv4Masks(): array
    {
        return [
            [0,  '00000000'],
            [1,  '80000000'], [2,  'c0000000'], [3,  'e0000000'], [4,  'f0000000'],
            [5,  'f8000000'], [6,  'fc000000'], [7,  'fe000000'], [8,  'ff000000'],
            [9,  'ff800000'], [10, 'ffc00000'], [11, 'ffe00000'], [12, 'fff00000'],
            [13, 'fff80000'], [14, 'fffc0000'], [15, 'fffe0000'], [16, 'ffff0000'],
            [17, 'ffff8000'], [18, 'ffffc000'], [19, 'ffffe000'], [20, 'fffff000'],
            [21, 'fffff800'], [22, 'fffffc00'], [23, 'fffffe00'], [24, 'ffffff00'],
            [25, 'ffffff80'], [26, 'ffffffc0'], [27, 'ffffffe0'], [28, 'fffffff0'],
            [29, 'fffffff8'], [30, 'fffffffc'], [31, 'fffffffe'], [32, 'ffffffff'],
        ];
    }

    public static function ipv4Bits(): array
    {
        return [
            [0,  '00000000'],
            [1,  '80000000'], [2,  '40000000'], [3,  '20000000'], [4,  '10000000'],
            [5,  '08000000'], [6,  '04000000'], [7,  '02000000'], [8,  '01000000'],
            [9,  '00800000'], [10, '00400000'], [11, '00200000'], [12, '00100000'],
            [13, '00080000'], [14, '00040000'], [15, '00020000'], [16, '00010000'],
            [17, '00008000'], [18, '00004000'], [19, '00002000'], [20, '00001000'],
            [21, '00000800'], [22, '00000400'], [23, '00000200'], [24, '00000100'],
            [25, '00000080'], [26, '00000040'], [27, '00000020'], [28, '00000010'],
            [29, '00000008'], [30, '00000004'], [31, '00000002'], [32, '00000001'],
        ];
    }
}
