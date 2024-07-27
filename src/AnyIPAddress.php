<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

/**
 * @property-read string $bytes
 * @method int compare(IPv4Address|IPv6Address $address, bool $strict = false)
 * @method int strictCompare(self $address)
 * @method int nonStrictCompare(IPv4Address|IPv6Address $address)
 * @method bool equals(IPv4Address|IPv6Address $address, bool $strict = false)
 * @method bool strictEquals(self $address)
 * @method bool nonStrictEquals(IPv4Address|IPv6Address $address)
 */
interface AnyIPAddress extends Stringable
{
    public function getBytes(): string;
    public function toString(): string;
    public function toRange(int $prefix = -1): IPv4Range|IPv6Range;
}
