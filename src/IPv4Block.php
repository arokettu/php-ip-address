<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Arokettu\IP\Helpers\BytesHelper;
use DomainException;

/**
 * @template-implements AnyIPBlock<IPv4Address>
 */
final readonly class IPv4Block implements AnyIPBlock
{
    use Helpers\IPBlockCommonTrait;

    private const TYPE = 'IPv4';
    private const BYTES = 4;
    private const BITS = 32;

    public string $maskBytes;

    public function __construct(
        public string $bytes,
        public int $prefix,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv4 block must be exactly 4 bytes');
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new DomainException('IPv4 prefix must be in range 0-32');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv4 block is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }

    /** @noinspection PhpHierarchyChecksInspection */
    public function strictContains(self|IPv4Address $addressOrBlock): bool
    {
        if ($addressOrBlock instanceof self && $addressOrBlock->prefix < $this->prefix) {
            // it's a wider block, definitely false
            return false;
        }

        return ($addressOrBlock->bytes & $this->maskBytes) === $this->bytes;
    }

    public function nonStrictContains(IPv4Address|IPv6Address|IPv4Block|IPv6Block $addressOrBlock): bool
    {
        if ($addressOrBlock instanceof self || $addressOrBlock instanceof IPv4Address) {
            return $this->strictContains($addressOrBlock);
        }

        return false;
    }

    public function getFirstAddress(): IPv4Address
    {
        return new IPv4Address($this->bytes);
    }

    public function getLastAddress(): IPv4Address
    {
        return new IPv4Address($this->bytes | ~$this->maskBytes);
    }

    public function toMappedIPv6(): IPv6Block
    {
        return IPv6Block::fromBytes(BytesHelper::MAPPED_BYTES_PREFIX . $this->bytes, $this->prefix + 96);
    }

    public function toCompatibleIPv6(): IPv6Block
    {
        return IPv6Block::fromBytes(BytesHelper::COMPATIBLE_BYTES_PREFIX . $this->bytes, $this->prefix + 96);
    }
}
