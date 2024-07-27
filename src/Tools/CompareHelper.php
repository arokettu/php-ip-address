<?php

declare(strict_types=1);

namespace Arokettu\IP\Tools;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Range;

final readonly class CompareHelper
{
    public static function compare(
        IPv4Address|IPv6Address|IPv4Range|IPv6Range $left,
        IPv4Address|IPv6Address|IPv4Range|IPv6Range $right,
        bool $strict = false,
    ): int {
        return $left->compare($right, $strict);
    }

    public static function strictCompare(
        IPv4Address|IPv6Address|IPv4Range|IPv6Range $left,
        IPv4Address|IPv6Address|IPv4Range|IPv6Range $right,
    ): int {
        return $left->strictCompare($right);
    }

    public static function nonStrictCompare(
        IPv4Address|IPv6Address|IPv4Range|IPv6Range $left,
        IPv4Address|IPv6Address|IPv4Range|IPv6Range $right,
    ): int {
        return $left->nonStrictCompare($right);
    }
}
