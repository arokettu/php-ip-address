<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

final readonly class IPv6Range implements AnyIPRange
{
    use Helpers\IPRangeCommonTrait;

    private const BYTES = 16;
    private const BITS = 128;
    private const ALL_ZEROS = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

    public string $maskBytes;

    public function __construct(
        public string $bytes,
        public int $mask,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 range must be exactly 16 bytes');
        }
        if ($mask < 0 || $mask > self::BITS) {
            throw new DomainException('IPv6 mask must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $mask);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv6 range is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }
}
