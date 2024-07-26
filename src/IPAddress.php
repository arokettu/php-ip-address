<?php

declare(strict_types=1);

namespace Arokettu\IP;

use DomainException;

final readonly class IPAddress
{
    // disable construct
    private function __construct()
    {
    }

    public static function fromBytes(string $bytes): IPv4Address|IPv6Address
    {
        return match (\strlen($bytes)) {
            4 => IPv4Address::fromBytes($bytes),
            16 => IPv6Address::fromBytes($bytes),
            default => throw new DomainException('IP address was not recognized'),
        };
    }

    public static function fromString(string $string): IPv4Address|IPv6Address
    {
        $bytes = inet_pton($string);
        if ($bytes === false) {
            throw new DomainException('$string must be a valid IP address');
        }
        return self::fromBytes($bytes);
    }
}
