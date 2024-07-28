<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Range;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Range;
use BadMethodCallException;
use UnexpectedValueException;

/**
 * @internal
 */
trait IPRangeCommonTrait
{
    public readonly string $bytes;
    public readonly int $prefix;
    public readonly string $maskBytes;

    abstract public function strictContains(self $addressOrRange): bool;
    abstract public function nonStrictContains(IPv4Address|IPv6Address|IPv4Range|IPv6Range $addressOrRange): bool;

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): self
    {
        if ($strict) {
            return new self($bytes, $prefix);
        }

        if (\strlen($bytes) !== self::BYTES) {
            throw new UnexpectedValueException(sprintf(
                'Base address for the %s range must be exactly %d bytes',
                self::TYPE,
                self::BYTES,
            ));
        }
        if ($prefix < 0) {
            if ($prefix < -self::BITS) {
                throw new UnexpectedValueException(sprintf(
                    'Negative prefix for the %s range must be greater than or equal to -%d',
                    self::TYPE,
                    self::BITS,
                ));
            }
            $prefix += self::BITS + 1;
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new UnexpectedValueException(sprintf('%s prefix must be in range 0-%d', self::TYPE, self::BITS));
        }

        $maskBytes = BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        return new self($bytes & $maskBytes, $prefix);
    }

    public static function fromString(string $string, int|null $prefix = null, bool $strict = false): self
    {
        if (str_contains($string, '/')) { // override mask
            if ($strict && $prefix !== null) {
                throw new BadMethodCallException(
                    'In strict mode prefix cannot appear in both string and $mask param'
                );
            }
            [$string, $prefixStr] = explode('/', $string, 2);
            $prefix = \intval($prefixStr); // succeeds but verify later
            if (!is_numeric($prefixStr) || $prefix != $prefixStr || $prefix < 0) { // non-strict here
                throw new UnexpectedValueException(sprintf('Prefix value "%s" appears to be invalid', $prefixStr));
            }
        }

        $bytes = inet_pton($string);
        if ($bytes === false || \strlen($bytes) !== self::BYTES) {
            throw new UnexpectedValueException(sprintf(
                'Base address "%s" does not appear to be a valid %s address',
                $string,
                self::TYPE,
            ));
        }

        return self::fromBytes($bytes, $prefix, $strict);
    }

    public function contains(IPv4Address|IPv6Address|IPv4Range|IPv6Range $addressOrRange, bool $strict = false): bool
    {
        return $strict ? $this->strictContains($addressOrRange) : $this->nonStrictContains($addressOrRange);
    }

    public function compare(IPv4Range|IPv6Range $range, bool $strict = false): int
    {
        return $strict ? $this->strictCompare($range) : $this->nonStrictCompare($range);
    }

    public function strictCompare(self $address): int
    {
        $compare = strcmp($this->bytes, $address->bytes) ?: $this->prefix <=> $address->prefix;
        return match (true) {
            $compare < 0 => -1,
            $compare > 0 => 1,
            default => 0,
        };
    }

    public function nonStrictCompare(IPv4Range|IPv6Range $address): int
    {
        return match (true) {
            $address instanceof self => $this->strictCompare($address),
            $address instanceof IPv4Range => 1,
            $address instanceof IPv6Range => -1,
        };
    }

    public function equals(IPv4Range|IPv6Range $range, bool $strict = false): bool
    {
        return $strict ? $this->strictEquals($range) : $this->nonStrictEquals($range);
    }

    public function strictEquals(self $range): bool
    {
        return $this->prefix === $range->prefix && $this->bytes === $range->bytes;
    }

    public function nonStrictEquals(IPv4Range|IPv6Range $range): bool
    {
        // just same, it will never be equal for different types
        return $this->prefix === $range->prefix && $this->bytes === $range->bytes;
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getPrefix(): int
    {
        return $this->prefix;
    }

    public function getMaskBytes(): string
    {
        return $this->maskBytes;
    }

    public function getMaskString(): string
    {
        return inet_ntop($this->maskBytes);
    }

    public function toString(): string
    {
        return sprintf("%s/%d", inet_ntop($this->bytes), $this->prefix);
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
