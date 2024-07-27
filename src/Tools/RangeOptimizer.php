<?php

declare(strict_types=1);

namespace Arokettu\IP\Tools;

use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Range;
use SplDoublyLinkedList;

final readonly class RangeOptimizer
{
    /**
     * @return array<IPv4Range>
     */
    public static function optimizeV4(IPv4Range ...$ranges): array
    {
        return self::optimize($ranges);
    }

    /**
     * @return array<IPv6Range>
     */
    public static function optimizeV6(IPv6Range ...$ranges): array
    {
        return self::optimize($ranges);
    }

    private static function optimize(array $ranges): array
    {
        usort($ranges, fn ($a, $b) => strcmp($a->bytes, $b->bytes) ?: $a->prefix <=> $b->prefix);

        /** @var SplDoublyLinkedList<IPv4Range> $list */
        $list = new SplDoublyLinkedList();

        foreach ($ranges as $range) {
            $list->push($range);
        }

        $list->rewind();

        while ($list->valid()) {
            /** @var IPv4Range $range */
            /** @var IPv4Range $nextRange */
            $range = $list->current();

            $list->next();
            if (!$list->valid()) {
                break;
            }

            do {
                $nextRange = $list->current();
                $contains = $range->contains($nextRange);
                $key = $list->key();
                if ($contains) {
                    $list->prev();
                    unset($list[$key]);
                }
                $list->next();
            } while ($contains && $list->valid());

            if ($range->prefix !== $nextRange->prefix) {
                continue;
            }
        }

        return [...$list];
    }
}
