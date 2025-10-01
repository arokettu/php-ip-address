<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP\Tools;

use Arokettu\IP\Helpers\BytesHelper;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Block;

final readonly class BlockOptimizer
{
    /**
     * @return list<IPv4Block|IPv6Block>
     */
    public static function optimize(IPv4Block|IPv6Block ...$blocks): array
    {
        // separate v4 and v6
        [$ipv4, $ipv6] = array_reduce($blocks, static function ($acc, $block) {
            if ($block instanceof IPv6Block) {
                $acc[1][] = $block;
            } else {
                $acc[0][] = $block;
            }
            return $acc;
        }, [[], []]);

        return array_merge(
            self::doOptimize($ipv4),
            self::doOptimize($ipv6),
        );
    }

    /**
     * @return list<IPv4Block>
     */
    public static function optimizeV4(IPv4Block ...$blocks): array
    {
        return self::doOptimize($blocks);
    }

    /**
     * @return list<IPv6Block>
     */
    public static function optimizeV6(IPv6Block ...$blocks): array
    {
        return self::doOptimize($blocks);
    }

    private static function doOptimize(array $blocks): array
    {
        /** @var list<IPv4Block>|list<IPv6Block> $blocks */

        $count = \count($blocks);
        if ($count < 2) {
            // none or single block does not need optimization
            return $blocks;
        }

        $bytes = \strlen($blocks[0]->bytes); // guaranteed to be same length

        CompareHelper::sort($blocks, strict: true);

        // absorb smaller blocks
        $prevBlock = $blocks[0];
        for ($index = 1; $index < $count; $index++) {
            $block = $blocks[$index];
            if ($prevBlock->strictContains($block)) {
                unset($blocks[$index]);
            } else {
                $prevBlock = $block;
            }
        }

        $blocks = array_values($blocks);

        $count = \count($blocks);
        if ($count < 2) {
            // none or single block does not need optimization
            return $blocks;
        }
        $prevBlock = $blocks[0];
        $prevIndex = 0;
        $index = 1;
        do {
            $block = $blocks[$index];

            if (
                // only networks with the same prefix can be merged
                $block->prefix !== $prevBlock->prefix ||
                // only the last significant bit of the prefix value can be different
                ($block->bytes ^ $prevBlock->bytes) !== BytesHelper::buildBitAtPosition($bytes, $block->prefix)
            ) {
                $prevIndex = $index;
                $prevBlock = $block;
                $index += 1;
                continue;
            }

            // merge
            $newBlock = new ($block::class)($prevBlock->bytes, $prevBlock->prefix - 1);
            $blocks[$prevIndex] = $newBlock;
            unset($blocks[$index]);

            // reset the loop
            $blocks = array_values($blocks);
            $prevIndex = max($prevIndex - 1, 0);
            $prevBlock = $blocks[$prevIndex];
            $index = $prevIndex + 1;
            $count -= 1;
            if ($count < 2) {
                // none or single block does not need optimization
                return $blocks;
            }
        } while ($index < $count);

        return $blocks;
    }
}
