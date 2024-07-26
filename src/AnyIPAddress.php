<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Stringable;

interface AnyIPAddress extends Stringable
{
    public function getBytes(): string;
    public function toString(): string;
}
