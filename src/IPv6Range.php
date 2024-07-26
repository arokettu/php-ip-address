<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;
use InvalidArgumentException;

/**
 * @template-implements AnyIPRange<IPv6Address>
 */
final readonly class IPv6Range implements AnyIPRange
{
    use Helpers\IPRangeCommonTrait;

    private const BYTES = 16;
    private const BITS = 128;

    public string $maskBytes;

    public function __construct(
        public string $bytes,
        public int $prefix,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 range must be exactly 16 bytes');
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new DomainException('IPv6 prefix must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $prefix);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv6 range is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): self
    {
        if ($strict) {
            return new self($bytes, $prefix);
        }

        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 range must be exactly 16 bytes');
        }
        if ($prefix < 0) {
            if ($prefix < -self::BITS) {
                throw new DomainException('Negative prefix for the IPv6 range must be greater than or equal to -128');
            }
            $prefix += self::BITS + 1;
        }
        if ($prefix < 0 || $prefix > self::BITS) {
            throw new DomainException('IPv6 prefix must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $prefix);

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
                'Base address "%s" does not appear to be a valid IPv6 address',
                $string,
            ));
        }

        return self::fromBytes($bytes, $prefix, $strict);
    }

    public function equals(self $range): bool
    {
        return $this->prefix === $range->prefix && $this->bytes === $range->bytes;
    }

    public function contains(self|IPv6Address $address): bool
    {
        if ($address instanceof self && $address->prefix < $this->prefix) {
            // it's a wider range, definitely false
            return false;
        }

        return ($address->bytes & $this->maskBytes) === $this->bytes;
    }

    public function getFirstAddress(): IPv6Address
    {
        return new IPv6Address($this->bytes);
    }

    public function getLastAddress(): IPv6Address
    {
        return new IPv6Address($this->bytes | ~$this->maskBytes);
    }
}
