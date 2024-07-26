<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

/**
 * @method int compare(self $address)
 * @method bool equals(self $address)
 */
interface AnyIPAddress extends Stringable
{
    public function getBytes(): string;
    public function toString(): string;
}
