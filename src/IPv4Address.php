<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

final readonly class IPv4Address implements AnyIPAddress
{
    use Helpers\IPAddressCommonTrait;

    private const BYTES = 4;

    public function __construct(
        public string $bytes
    ) {
        if (\strlen($bytes) !== 4) {
            throw new DomainException('IPv4 address must be exactly 4 bytes');
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
            throw new DomainException('$string must be a valid IPv4 address');
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
