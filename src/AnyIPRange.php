<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

/**
 * @template T
 * @method bool equals(self $range)
 * @method bool contains(T|self $address)
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
