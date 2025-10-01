<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

/**
 * @template T
 * @property-read string $bytes
 * @property-read int $prefix
 * @method bool equals(IPv4Block|IPv6Block $block, bool $strict = false)
 * @method bool strictEquals(self $block)
 * @method bool nonStrictEquals(IPv4Block|IPv6Block $block)
 * @method bool contains(IPv4Address|IPv6Address|IPv4Block|IPv6Block $addressOrBlock, bool $strict = false)
 * @method bool strictContains(T|self $addressOrBlock)
 * @method bool nonStrictContains(IPv4Address|IPv6Address|IPv4Block|IPv6Block $addressOrBlock)
 * @method T getFirstAddress()
 * @method T getLastAddress()
 */
interface AnyIPBlock extends Stringable
{
    public function toString(): string;

    public function getBytes(): string;
    public function getPrefix(): int;
    public function getMaskBytes(): string;
    public function getMaskString(): string;
}
