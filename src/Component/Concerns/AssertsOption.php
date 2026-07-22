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
        if (in_array($value, $allowed, true)) {
            return;
        }

        $component = (new \ReflectionClass(static::class))->getShortName();
        $hint = self::closestOption($value, $allowed);
        $message = sprintf(
            'Invalid %s %s "%s". Allowed: %s.',
            $component,
            $name,
            $value,
            implode(', ', $allowed),
        );

        if ($hint !== null) {
            $message .= sprintf(' Did you mean "%s"?', $hint);
        }

        $message .= sprintf(' Tip: call %s::%s() with one of the allowed values.', $component, $name);

        throw new InvalidArgumentException($message);
    }

    /**
     * @param list<string> $allowed
     */
    private static function closestOption(string $value, array $allowed): ?string
    {
        $best = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($allowed as $candidate) {
            $distance = levenshtein(strtolower($value), strtolower($candidate));
            if ($distance < $bestDistance && $distance <= 3) {
                $bestDistance = $distance;
                $best = $candidate;
            }
        }

        return $best;
    }
}
