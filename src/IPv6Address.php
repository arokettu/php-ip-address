<?php

declare(strict_types=1);

namespace Arokettu\IP;

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
}
