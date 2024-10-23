<?php

declare(strict_types=1);

namespace Arokettu\IP;

use Arokettu\IP\Helpers\BytesHelper;
use DomainException;

final readonly class IPv6Address implements AnyIPAddress
{
    use Helpers\IPAddressCommonTrait;

    private const TYPE = 'IPv6';
    private const BYTES = 16;

    public function __construct(
        public string $bytes
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('IPv6 address must be exactly 16 bytes');
        }
    }

    public function toBlock(int $prefix = -1): IPv6Block
    {
        return IPv6Block::fromBytes($this->bytes, $prefix);
    }

    public function isMappedIPv4(): bool
    {
        return strncmp($this->bytes, BytesHelper::MAPPED_BYTES_PREFIX, 12) === 0;
    }

    public function isCompatibleIPv4(): bool
    {
        return strncmp($this->bytes, BytesHelper::COMPATIBLE_BYTES_PREFIX, 12) === 0;
    }

    public function isIPv4(): bool
    {
        return $this->isMappedIPv4();
    }

    public function getIPv4(): IPv4Address
    {
        if (!$this->isIPv4()) {
            throw new DomainException('This IPv6 address does not encode IPv4');
        }

        return IPv4Address::fromBytes(substr($this->bytes, 12));
    }
}
