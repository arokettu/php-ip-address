<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Arokettu\IP\Helpers\BytesHelper;
use DomainException;

final readonly class IPv4Range
{
    private const BYTES = 4;
    private const BITS = 32;
    private const ALL_ZEROS = "\0\0\0\0";

    public string $maskBytes;

    public function __construct(
        public string $bytes,
        public int $mask,
    ) {
        if (\strlen($bytes) !== 4) {
            throw new DomainException('Base address for the IPv4 range must be exactly 4 bytes.');
        }
        if ($mask < 0 || $mask > self::BITS) {
            throw new DomainException('IPv4 mask must be exactly 4 bytes.');
        }

        $maskBytes = BytesHelper::buildMaskBytes(self::BYTES, $mask);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv4 range is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }

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
