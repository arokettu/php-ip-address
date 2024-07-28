<?php

declare(strict_types=1);

namespace Arokettu\IP;

use UnexpectedValueException;

final readonly class IPRange
{
    /**
     * Disable constructor
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): IPv4Range|IPv6Range
    {
        return match (\strlen($bytes)) {
            4 => IPv4Range::fromBytes($bytes, $prefix, $strict),
            16 => IPv6Range::fromBytes($bytes, $prefix, $strict),
            default => throw new UnexpectedValueException(sprintf(
                'IP range was not recognized, %d is not a valid byte length',
                \strlen($bytes),
            )),
        };
    }

    public static function fromString(
        string $string,
        int|null $prefix = null,
        bool $strict = false
    ): IPv4Range|IPv6Range {
        try {
            return IPv4Range::fromString($string, $prefix, $strict);
        } catch (UnexpectedValueException $e4) {
            // ignore
        }

        try {
            return IPv6Range::fromString($string, $prefix, $strict);
        } catch (UnexpectedValueException $e6) {
            // ignore
        }

        throw new UnexpectedValueException(sprintf(
            'IP range was not recognized: "%s", "%s"',
            $e4->getMessage(),
            $e6->getMessage(),
        ), previous: $e6);
    }
}
