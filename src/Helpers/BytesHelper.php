<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

/**
 * @internal
 */
final class BytesHelper
{
    public const COMPATIBLE_BYTES_PREFIX = "\0\0\0\0\0\0\0\0\0\0\0\0";
    public const MAPPED_BYTES_PREFIX = "\0\0\0\0\0\0\0\0\0\0\xff\xff";
    public const IPV6_LOCALHOST_SUFFIX = "\0\0\0\1";
    public const IPV6_ZERO_SUFFIX = "\0\0\0\0";

    private static array $maskBytes = [];
    private static array $bitAtPosition = [];

    public static function buildMaskBytes(int $bytes, int $prefix): string
    {
        if (!isset(self::$maskBytes[$bytes][$prefix])) {
            $full = intdiv($prefix, 8); // 0xff bytes

            $maskBytes = str_repeat("\xff", $full);
            $bytes -= $full;

            $partial = $prefix % 8;
            if ($partial !== 0) { // byte with bits both set and unset
                $maskBytes .= \chr(~((1 << (8 - $partial)) - 1) & 0xff);
                $bytes -= 1;
            }

            $maskBytes .= str_repeat("\0", $bytes); // 0x00 bytes

            self::$maskBytes[$bytes][$prefix] = $maskBytes;
        }

        return self::$maskBytes[$bytes][$prefix];
    }

    public static function buildBitAtPosition(int $bytes, int $position): string
    {
        if (!isset(self::$bitAtPosition[$bytes][$position])) {
            $bytes = str_repeat("\0", $bytes);

            if ($position !== 0) {
                $position -= 1;
                $byte = intdiv($position, 8);
                $value = $position % 8;
                $bytes[$byte] = \chr(1 << (7 - $value));
            }

            self::$bitAtPosition[$bytes][$position] = $bytes;
        }

        return self::$bitAtPosition[$bytes][$position];
    }
}
