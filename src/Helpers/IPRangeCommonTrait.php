<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

trait IPRangeCommonTrait
{
    public readonly int $mask;
    public readonly string $bytes;
    public readonly string $maskBytes;


    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getMask(): int
    {
        return $this->mask;
    }

    public function getMaskBytes(): string
    {
        return $this->maskBytes;
    }

    public function toString(): string
    {
        return sprintf("%s/%d", inet_ntop($this->bytes), $this->mask);
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
