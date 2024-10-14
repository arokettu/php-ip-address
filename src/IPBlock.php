<?php

declare(strict_types=1);

namespace Arokettu\IP;

use UnexpectedValueException;

final readonly class IPBlock
{
    /**
     * Disable constructor
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function fromBytes(string $bytes, int $prefix, bool $strict = false): IPv4Block|IPv6Block
    {
        return match (\strlen($bytes)) {
            4 => IPv4Block::fromBytes($bytes, $prefix, $strict),
            16 => IPv6Block::fromBytes($bytes, $prefix, $strict),
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
    ): IPv4Block|IPv6Block {
        try {
            return IPv4Block::fromString($string, $prefix, $strict);
        } catch (UnexpectedValueException $e4) {
            // ignore
        }

        try {
            return IPv6Block::fromString($string, $prefix, $strict);
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
