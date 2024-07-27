<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv6Address;
use DomainException;

/**
 * @internal
 */
trait IPAddressCommonTrait
{
    public readonly string $bytes;

    public static function fromBytes(string $bytes): self
    {
        return new self($bytes);
    }

    public static function fromString(string $string): self
    {
        $bytes = inet_pton($string);
        if ($bytes === false || \strlen($bytes) !== self::BYTES) {
            throw new DomainException(sprintf(
                '$string must be a valid %s address, "%s" given',
                self::TYPE,
                $string,
            ));
        }
        return self::fromBytes($bytes);
    }

    public function compare(IPv4Address|IPv6Address $address, bool $strict = false): int
    {
        return $strict ? $this->strictCompare($address) : $this->nonStrictCompare($address);
    }

    public function strictCompare(self $address): int
    {
        $compare = strcmp($this->bytes, $address->bytes);
        return match (true) {
            $compare < 0 => -1,
            $compare > 0 => 1,
            default => 0,
        };
    }

    public function nonStrictCompare(IPv4Address|IPv6Address $address): int
    {
        return match (true) {
            $address instanceof self => $this->strictCompare($address),
            $address instanceof IPv4Address => 1,
            $address instanceof IPv6Address => -1,
        };
    }

    public function equals(IPv4Address|IPv6Address $address, bool $strict = false): bool
    {
        return $strict ? $this->strictEquals($address) : $this->nonStrictEquals($address);
    }

    public function strictEquals(self $address): bool
    {
        return $this->bytes === $address->bytes;
    }

    public function nonStrictEquals(IPv4Address|IPv6Address $address): bool
    {
        // just same, it will never be equal for different types
        return $this->bytes === $address->bytes;
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function toString(): string
    {
        return inet_ntop($this->bytes);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function __debugInfo(): array
    {
        return [
            'value' => $this->toString(),
        ];
    }
}
