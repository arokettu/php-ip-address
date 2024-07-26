<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

trait IPAddressCommonTrait
{
    public readonly string $bytes;

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function toString(): string
    {
        return inet_ntop($this->bytes);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function __debugInfo(): array
    {
        return [
            'value' => $this->toString(),
        ];
    }
}
