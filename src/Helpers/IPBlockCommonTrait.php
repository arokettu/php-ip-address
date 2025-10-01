<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

use Arokettu\IP\IPv4Address;
use Arokettu\IP\IPv4Block;
use Arokettu\IP\IPv6Address;
use Arokettu\IP\IPv6Block;
use BadMethodCallException;
use DomainException;
use UnexpectedValueException;

/**
 * @internal
 */
trait IPBlockCommonTrait
{
    public readonly string $bytes;
    public readonly int $prefix;
    public readonly string $maskBytes;

    abstract public function strictContains(self $addressOrBlock): bool;
    abstract public function nonStrictContains(IPv4Address|IPv6Address|IPv4Block|IPv6Block $addressOrBlock): bool;

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): self
    {
        if ($strict) {
            try {
                return new self($bytes, $prefix);
            } catch (DomainException $e) {
                throw new UnexpectedValueException($e->getMessage(), previous: $e);
            }
        }

        if (\strlen($bytes) !== self::BYTES) {
            throw new UnexpectedValueException(\sprintf(
                'Base address for the %s block must be exactly %d bytes',
                self::TYPE,
                self::BYTES,
            ));
        }
        if ($prefix < 0) {
            if ($prefix < -self::BITS) {
                throw new UnexpectedValueException(\sprintf(
                    'Negative prefix for the %s block must be greater than or equal to -%d',
                    self::TYPE,
                    self::BITS,
                ));
            }
            $prefix += self::BITS + 1;
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new UnexpectedValueException(\sprintf('%s prefix must be in range 0-%d', self::TYPE, self::BITS));
        }

        $maskBytes = BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        return new self($bytes & $maskBytes, $prefix);
    }

    public static function fromString(string $string, int|null $prefix = null, bool $strict = false): self
    {
        if (str_contains($string, '/')) { // override mask
            if ($strict && $prefix !== null) {
                throw new BadMethodCallException(
                    'In strict mode prefix cannot appear in both string and $mask param',
                );
            }
            [$string, $prefixStr] = explode('/', $string, 2);
            $prefix = \intval($prefixStr); // succeeds but verify later
            if (!is_numeric($prefixStr) || $prefix != $prefixStr || $prefix < 0) { // non-strict here
                throw new UnexpectedValueException(\sprintf('Prefix value "%s" appears to be invalid', $prefixStr));
            }
        }

        $bytes = inet_pton($string);
        if ($bytes === false || \strlen($bytes) !== self::BYTES) {
            throw new UnexpectedValueException(\sprintf(
                'Base address "%s" does not appear to be a valid %s address',
                $string,
                self::TYPE,
            ));
        }

        return self::fromBytes($bytes, $prefix, $strict);
    }

    public function contains(IPv4Address|IPv6Address|IPv4Block|IPv6Block $addressOrBlock, bool $strict = false): bool
    {
        return $strict ? $this->strictContains($addressOrBlock) : $this->nonStrictContains($addressOrBlock);
    }

    public function compare(IPv4Block|IPv6Block $block, bool $strict = false): int
    {
        return $strict ? $this->strictCompare($block) : $this->nonStrictCompare($block);
    }

    public function strictCompare(self $block): int
    {
        $compare = strcmp($this->bytes, $block->bytes) ?: $this->prefix <=> $block->prefix;
        return match (true) {
            $compare < 0 => -1,
            $compare > 0 => 1,
            default => 0,
        };
    }

    public function nonStrictCompare(IPv4Block|IPv6Block $block): int
    {
        return match (true) {
            $block instanceof self => $this->strictCompare($block),
            $block instanceof IPv4Block => 1,
            $block instanceof IPv6Block => -1,
        };
    }

    public function equals(IPv4Block|IPv6Block $block, bool $strict = false): bool
    {
        return $strict ? $this->strictEquals($block) : $this->nonStrictEquals($block);
    }

    public function strictEquals(self $block): bool
    {
        return $this->prefix === $block->prefix && $this->bytes === $block->bytes;
    }

    public function nonStrictEquals(IPv4Block|IPv6Block $block): bool
    {
        // just same, it will never be equal for different types
        return $this->prefix === $block->prefix && $this->bytes === $block->bytes;
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

    public function isSingleAddress(): bool
    {
        return $this->prefix === self::BITS;
    }

    public function toString(): string
    {
        return \sprintf('%s/%d', inet_ntop($this->bytes), $this->prefix);
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
