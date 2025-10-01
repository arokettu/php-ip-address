<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

final class Formatter
{
    public static function v6ToFullHexString(string $ipv6bytes): string
    {
        return implode(':', str_split(bin2hex($ipv6bytes), 4));
    }
}
