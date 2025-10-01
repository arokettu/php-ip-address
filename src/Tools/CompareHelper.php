<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Tools;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Block;

final readonly class CompareHelper
{
    public static function compare(
        IPv4Address|IPv6Address|IPv4Block|IPv6Block $left,
        IPv4Address|IPv6Address|IPv4Block|IPv6Block $right,
        bool $strict = false,
    ): int {
        return $left->compare($right, $strict);
    }

    public static function strictCompare(
        IPv4Address|IPv6Address|IPv4Block|IPv6Block $left,
        IPv4Address|IPv6Address|IPv4Block|IPv6Block $right,
    ): int {
        return $left->strictCompare($right);
    }

    public static function nonStrictCompare(
        IPv4Address|IPv6Address|IPv4Block|IPv6Block $left,
        IPv4Address|IPv6Address|IPv4Block|IPv6Block $right,
    ): int {
        return $left->nonStrictCompare($right);
    }

    public static function sort(array &$array, bool $strict = false): void
    {
        usort($array, $strict ? self::strictCompare(...) : self::nonStrictCompare(...));
    }

    public static function asort(array &$array, bool $strict = false): void
    {
        uasort($array, $strict ? self::strictCompare(...) : self::nonStrictCompare(...));
    }
}
