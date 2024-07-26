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

    public static function fromBytes(string $bytes, int $mask, bool $strict = false): IPv4Range|IPv6Range
    {
        return match (\strlen($bytes)) {
            4 => IPv4Range::fromBytes($bytes, $mask, $strict),
            16 => IPv6Range::fromBytes($bytes, $mask, $strict),
            default => throw new DomainException('IP range was not recognized'),
        };
    }
}
