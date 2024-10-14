<?php

declare(strict_types=1);

namespace Arokettu\IP\Tools;

use Arokettu\IP\Helpers\BytesHelper;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;

final readonly class BlockOptimizer
{
    /**
     * @return array<IPv4Block>
     */
    public static function optimizeV4(IPv4Block ...$ranges): array
    {
        return self::optimize($ranges);
    }

    /**
     * @return array<IPv6Block>
     */
    public static function optimizeV6(IPv6Block ...$ranges): array
    {
        return self::optimize($ranges);
    }

    private static function optimize(array $ranges): array
    {
        /** @var list<IPv4Block>|list<IPv6Block> $ranges */

        $count = \count($ranges);
        if ($count < 2) {
            // none or single range does not need optimization
            return $ranges;
        }

        $bytes = \strlen($ranges[0]->bytes); // guaranteed to be same length

        CompareHelper::sort($ranges, strict: true);

        // absorb smaller ranges
        $prevRange = $ranges[0];
        for ($index = 1; $index < $count; $index++) {
            $range = $ranges[$index];
            if ($prevRange->strictContains($range)) {
                unset($ranges[$index]);
            } else {
                $prevRange = $range;
            }
        }

        $ranges = array_values($ranges);

        $count = \count($ranges);
        if ($count < 2) {
            // none or single range does not need optimization
            return $ranges;
        }
        $prevRange = $ranges[0];
        $prevIndex = 0;
        $index = 1;
        do {
            $range = $ranges[$index];

            if (
                // only networks with the same prefix can be merged
                $range->prefix !== $prevRange->prefix ||
                // only the last significant bit of the prefix value can be different
                ($range->bytes ^ $prevRange->bytes) !== BytesHelper::buildBitAtPosition($bytes, $range->prefix)
            ) {
                $prevIndex = $index;
                $prevRange = $range;
                $index += 1;
                continue;
            }

            // merge
            $newRange = new ($range::class)($prevRange->bytes, $prevRange->prefix - 1);
            $ranges[$prevIndex] = $newRange;
            unset($ranges[$index]);

            // reset the loop
            $ranges = array_values($ranges);
            $prevIndex = max($prevIndex - 1, 0);
            $prevRange = $ranges[$prevIndex];
            $index = $prevIndex + 1;
            $count -= 1;
            if ($count < 2) {
                // none or single range does not need optimization
                return $ranges;
            }
        } while ($index < $count);

        return $ranges;
    }
}
