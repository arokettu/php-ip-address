<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP;

use Arokettu\IP\Helpers\BytesHelper;
use DomainException;

final readonly class IPv4Address implements AnyIPAddress
{
    use Helpers\IPAddressCommonTrait;

    private const TYPE = 'IPv4';
    private const BYTES = 4;

    public function __construct(
        public string $bytes,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('IPv4 address must be exactly 4 bytes');
        }
    }

    public function toBlock(int $prefix = -1): IPv4Block
    {
        return IPv4Block::fromBytes($this->bytes, $prefix);
    }

    public function toMappedIPv6(): IPv6Address
    {
        return IPv6Address::fromBytes(BytesHelper::MAPPED_BYTES_PREFIX . $this->bytes);
    }

    public function toCompatibleIPv6(): IPv6Address
    {
        return IPv6Address::fromBytes(BytesHelper::COMPATIBLE_BYTES_PREFIX . $this->bytes);
    }
}
