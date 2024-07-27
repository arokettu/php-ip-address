<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

/**
 * @template T
 * @property-read string $bytes
 * @property-read int $prefix
 * @method bool equals(IPv4Range|IPv6Range $range, bool $strict = false)
 * @method bool strictEquals(self $range)
 * @method bool nonStrictEquals(IPv4Range|IPv6Range $range)
 * @method bool contains(IPv4Address|IPv6Address|IPv4Range|IPv6Range $address, bool $strict = false)
 * @method bool strictContains(T|self $address)
 * @method bool nonStrictContains(IPv4Address|IPv6Address|IPv4Range|IPv6Range $address)
 * @method T getFirstAddress()
 * @method T getLastAddress()
 */
interface AnyIPRange extends Stringable
{
    public function getBytes(): string;
    public function getPrefix(): int;
    public function getMaskBytes(): string;
    public function toString(): string;
}
