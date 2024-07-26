<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

/**
 * @internal
 */
final readonly class BytesHelper
{
    public static function buildMaskBytes(int $bytes, int $mask): string
    {
        $full = intdiv($mask, 8); // 0xff bytes

        $maskBytes = str_repeat("\xff", $full);
        $bytes -= $full;

        $partial = $mask % 8;
        if ($partial !== 0) { // byte with bits both set and unset
            $maskBytes .= \chr(~(2 ** (8 - $partial) - 1));
            $bytes -= 1;
        }

        $maskBytes .= str_repeat("\0", $bytes); // 0x00 bytes

        return $maskBytes;
    }
}
