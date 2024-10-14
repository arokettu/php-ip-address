<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

/**
 * @template-implements AnyIPBlock<IPv6Address>
 */
final readonly class IPv6Block implements AnyIPBlock
{
    use Helpers\IPBlockCommonTrait;

    private const TYPE = 'IPv6';
    private const BYTES = 16;
    private const BITS = 128;

    public string $maskBytes;

    public function __construct(
        public string $bytes,
        public int $prefix,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 block must be exactly 16 bytes');
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new DomainException('IPv6 prefix must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv6 block is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }

    /** @noinspection PhpHierarchyChecksInspection */
    public function strictContains(self|IPv6Address $addressOrBlock): bool
    {
        if ($addressOrBlock instanceof self && $addressOrBlock->prefix < $this->prefix) {
            // it's a wider block, definitely false
            return false;
        }

        return ($addressOrBlock->bytes & $this->maskBytes) === $this->bytes;
    }

    public function nonStrictContains(IPv4Address|IPv6Address|IPv4Block|IPv6Block $addressOrBlock): bool
    {
        if ($addressOrBlock instanceof self || $addressOrBlock instanceof IPv6Address) {
            return $this->strictContains($addressOrBlock);
        }

        return false;
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
