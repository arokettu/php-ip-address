<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

/**
 * @property-read string $bytes
 * @method int compare(self $address)
 * @method int strictCompare(self $address)
 * @method int nonStrictCompare(self $address)
 * @method bool equals(self $address)
 * @method bool strictEquals(self $address)
 * @method bool nonStrictEquals(self $address)
 */
interface AnyIPAddress extends Stringable
{
    public function getBytes(): string;
    public function toString(): string;
    public function toRange(): IPv4Range|IPv6Range;
}
