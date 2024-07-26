<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

final readonly class IPRange
{
    // disable construct
    private function __construct()
    {
    }

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): IPv4Range|IPv6Range
    {
        return match (\strlen($bytes)) {
            4 => IPv4Range::fromBytes($bytes, $prefix, $strict),
            16 => IPv6Range::fromBytes($bytes, $prefix, $strict),
            default => throw new DomainException('IP range was not recognized'),
        };
    }

    public static function fromString(
        string $string,
        int|null $prefix = null,
        bool $strict = false
    ): IPv4Range|IPv6Range {
        try {
            return IPv4Range::fromString($string, $prefix, $strict);
        } catch (DomainException $e4) {
            // ignore
        }

        try {
            return IPv6Range::fromString($string, $prefix, $strict);
        } catch (DomainException $e6) {
            // ignore
        }

        throw new DomainException(sprintf(
            'IP range was not recognized: "%s", "%s"',
            $e4->getMessage(),
            $e6->getMessage(),
        ), previous: $e6);
    }
}
