<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

use DomainException;

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
                '$string must be a valid %s address',
                self::TYPE,
            ));
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

    public function equals(self $address): bool
    {
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
