<?php

declare(strict_types=1);

namespace Arokettu\IP\Helpers;

use DomainException;
use InvalidArgumentException;

trait IPRangeCommonTrait
{
    public readonly string $bytes;
    public readonly int $prefix;
    public readonly string $maskBytes;

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): self
    {
        if ($strict) {
            return new self($bytes, $prefix);
        }

        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException(sprintf(
                'Base address for the %s range must be exactly %d bytes',
                self::TYPE,
                self::BYTES,
            ));
        }
        if ($prefix < 0) {
            if ($prefix < -self::BITS) {
                throw new DomainException(sprintf(
                    'Negative prefix for the %s range must be greater than or equal to -%d',
                    self::TYPE,
                    self::BITS,
                ));
            }
            $prefix += self::BITS + 1;
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new DomainException(sprintf('%s prefix must be in range 0-%d', self::TYPE, self::BITS));
        }

        $maskBytes = BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        return new self($bytes & $maskBytes, $prefix);
    }

    public static function fromString(string $string, int|null $prefix = null, bool $strict = false): self
    {
        if (str_contains($string, '/')) { // override mask
            if ($strict && $prefix !== null) {
                throw new InvalidArgumentException(
                    'In strict mode prefix cannot appear in both string and $mask param'
                );
            }
            [$string, $prefixStr] = explode('/', $string, 2);
            $prefix = \intval($prefixStr); // succeeds but verify later
            if (!is_numeric($prefixStr) || $prefix != $prefixStr || $prefix < 0) { // non-strict here
                throw new DomainException(sprintf('Prefix value "%s" appears to be invalid', $prefixStr));
            }
        }

        $bytes = inet_pton($string);
        if ($bytes === false || \strlen($bytes) !== self::BYTES) {
            throw new DomainException(sprintf(
                'Base address "%s" does not appear to be a valid %s address',
                $string,
                self::TYPE,
            ));
        }

        return self::fromBytes($bytes, $prefix, $strict);
    }

    public function equals(self $range): bool
    {
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