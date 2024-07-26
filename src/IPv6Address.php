<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

final readonly class IPv6Address implements AnyIPAddress
{
    use Helpers\IPAddressCommonTrait;

    private const BYTES = 16;

    public function __construct(
        public string $bytes
    ) {
        if (\strlen($bytes) !== 16) {
            throw new DomainException('IPv6 address must be exactly 16 bytes');
        }
    }

    public static function fromBytes(string $bytes): self
    {
        return new self($bytes);
    }

    public static function fromString(string $string): self
    {
        $bytes = inet_pton($string);
        if ($bytes === false || \strlen($bytes) !== self::BYTES) {
            throw new DomainException('$string must be a valid IPv6 address');
        }
        return self::fromBytes($bytes);
    }

    public function compare(self $address): int
    {
        $compare = strcmp($this->bytes, $address->bytes);
        return match (true) {
            $compare < 0 => -1,
            $compare > 0 => 1,
            default => 0,
        };
    }
}
