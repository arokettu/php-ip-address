<?php

/**
 * @copyright 2024 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\IP;

use UnexpectedValueException;

final readonly class IPAddress
{
    /**
     * Disable constructor
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function fromBytes(string $bytes): IPv4Address|IPv6Address
    {
        return match (\strlen($bytes)) {
            4 => IPv4Address::fromBytes($bytes),
            16 => IPv6Address::fromBytes($bytes),
            default => throw new UnexpectedValueException(\sprintf(
                'IP address was not recognized, %d is not a valid byte length',
                \strlen($bytes),
            )),
        };
    }

    public static function fromString(string $string): IPv4Address|IPv6Address
    {
        $bytes = inet_pton($string);
        if ($bytes === false) {
            throw new UnexpectedValueException(\sprintf(
                '$string must be a valid IP address, "%s" given',
                $string,
            ));
        }
        return self::fromBytes($bytes);
    }
}
