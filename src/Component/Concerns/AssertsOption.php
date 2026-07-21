<?php

declare(strict_types=1);

namespace Serixa\Component\Concerns;

use Serixa\Exception\InvalidArgumentException;

/**
 * Validates semantic option values against an allow-list.
 *
 * @internal
 */
trait AssertsOption
{
    /**
     * @param list<string> $allowed
     */
    private function assertOption(string $value, array $allowed, string $name): void
    {
        if (! in_array($value, $allowed, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid %s %s "%s". Allowed: %s',
                (new \ReflectionClass(static::class))->getShortName(),
                $name,
                $value,
                implode(', ', $allowed),
            ));
        }
    }
}
