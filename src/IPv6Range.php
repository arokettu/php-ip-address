<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

/**
 * @template-implements AnyIPRange<IPv6Address>
 */
final readonly class IPv6Range implements AnyIPRange
{
    use Helpers\IPRangeCommonTrait;

    private const TYPE = 'IPv6';
    private const BYTES = 16;
    private const BITS = 128;

    public string $maskBytes;

    public function __construct(
        public string $bytes,
        public int $prefix,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 range must be exactly 16 bytes');
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new DomainException('IPv6 prefix must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv6 range is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }

    public function contains(self|IPv6Address $address): bool
    {
        if ($address instanceof self && $address->prefix < $this->prefix) {
            // it's a wider range, definitely false
            return false;
        }

        return ($address->bytes & $this->maskBytes) === $this->bytes;
    }

    public function getFirstAddress(): IPv6Address
    {
        return new IPv6Address($this->bytes);
    }

    public function getLastAddress(): IPv6Address
    {
        return new IPv6Address($this->bytes | ~$this->maskBytes);
    }
}