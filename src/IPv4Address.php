<?php

declare(strict_types=1);

namespace Arokettu\IP;

use UnexpectedValueException;

final readonly class IPv4Address implements AnyIPAddress
{
    use Helpers\IPAddressCommonTrait;

    private const TYPE = 'IPv4';
    private const BYTES = 4;

    public function __construct(
        public string $bytes
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new UnexpectedValueException('IPv4 address must be exactly 4 bytes');
        }
    }

    public function toRange(int $prefix = -1): IPv4Range
    {
        return IPv4Range::fromBytes($this->bytes, $prefix);
    }
}
