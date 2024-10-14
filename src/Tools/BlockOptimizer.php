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
    public static function optimizeV4(IPv4Block ...$blocks): array
    {
        return self::optimize($blocks);
    }

    /**
     * @return array<IPv6Block>
     */
    public static function optimizeV6(IPv6Block ...$blocks): array
    {
        return self::optimize($blocks);
    }

    private static function optimize(array $blocks): array
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
