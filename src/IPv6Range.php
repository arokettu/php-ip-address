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
        public int $mask,
    ) {
        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 range must be exactly 16 bytes');
        }
        if ($mask < 0 || $mask > self::BITS) {
            throw new DomainException('IPv6 mask must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $mask);

        if (($maskBytes & $bytes) !== $bytes) {
            throw new DomainException('IPv6 range is not in a normalized form');
        }

        $this->maskBytes = $maskBytes;
    }

    public static function fromBytes(string $bytes, int $mask, bool $strict = false): self
    {
        if ($strict) {
            return new self($bytes, $mask);
        }

        if (\strlen($bytes) !== self::BYTES) {
            throw new DomainException('Base address for the IPv6 range must be exactly 16 bytes');
        }
        if ($mask < 0) {
            if ($mask < -self::BITS) {
                throw new DomainException('Negative mask for the IPv6 range must be greater than or equal to -128');
            }
            $mask += self::BITS + 1;
        }
        if ($mask < 0 || $mask > self::BITS) {
            throw new DomainException('IPv6 mask must be in range 0-128');
        }

        $maskBytes = Helpers\BytesHelper::buildMaskBytes(self::BYTES, $mask);

        return new self($bytes & $maskBytes, $mask);
    }

    public static function fromString(string $string, int|null $mask = null, bool $strict = false): self
    {
        if (str_contains($string, '/')) { // override mask
            if ($strict && $mask !== null) {
                throw new InvalidArgumentException('In strict mode mask cannot appear in both string and $mask param');
            }
            [$string, $maskStr] = explode('/', $string, 2);
            $mask = \intval($maskStr); // succeeds but verify later
            if (!is_numeric($maskStr) || $mask != $maskStr || $mask < 0) { // non-strict here
                throw new DomainException(sprintf('Mask value "%s" appears to be invalid', $maskStr));
            }
        }

        $bytes = inet_pton($string);
        if ($bytes === false || \strlen($bytes) !== self::BYTES) {
            throw new DomainException(sprintf(
                'Base address "%s" does not appear to be a valid IPv6 address',
                $string,
            ));
        }

        return self::fromBytes($bytes, $mask, $strict);
    }

    public function equals(self $range): bool
    {
        return $this->mask === $range->mask && $this->bytes === $range->bytes;
    }

    public function contains(self|IPv6Address $address): bool
    {
        if ($address instanceof self && $address->mask < $this->mask) {
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
