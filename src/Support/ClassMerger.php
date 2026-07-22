<?php

declare(strict_types=1);

namespace Serixa\Support;

/**
 * Merges CSS class tokens with Tailwind-aware conflict resolution.
 *
 * Exact duplicates are dropped. Within a conflict group (e.g. width utilities),
 * a later token replaces an earlier one so consumer classes can override theme defaults.
 */
final class ClassMerger
{
    /**
     * @param list<string> ...$groups
     * @return list<string>
     */
    public function merge(array ...$groups): array
    {
        /** @var list<string> $ordered */
        $ordered = [];
        /** @var array<string, int> $groupIndex class-group => position in $ordered */
        $groupIndex = [];
        /** @var array<string, true> $seen exact class dedupe */
        $seen = [];

        foreach ($groups as $group) {
            foreach ($group as $class) {
                $class = trim($class);

                if ($class === '' || isset($seen[$class])) {
                    continue;
                }

                $conflictGroup = self::conflictGroup($class);

                if ($conflictGroup !== null && isset($groupIndex[$conflictGroup])) {
                    $replaceAt = $groupIndex[$conflictGroup];
                    $previous = $ordered[$replaceAt];
                    unset($seen[$previous]);
                    $ordered[$replaceAt] = $class;
                    $seen[$class] = true;

                    continue;
                }

                $seen[$class] = true;
                $ordered[] = $class;

                if ($conflictGroup !== null) {
                    $groupIndex[$conflictGroup] = array_key_last($ordered);
                }
            }
        }

        return array_values($ordered);
    }

    /**
     * @param list<string> ...$groups
     */
    public function toString(array ...$groups): string
    {
        return implode(' ', $this->merge(...$groups));
    }

    /**
     * Map a utility class to a conflict group id, or null if it never conflicts.
     */
    private static function conflictGroup(string $class): ?string
    {
        // Strip common variants: dark:, sm:, hover:, etc.
        $base = $class;
        while (($pos = strpos($base, ':')) !== false) {
            $base = substr($base, $pos + 1);
        }

        return match (true) {
            $base === 'w-full' || $base === 'w-auto' || $base === 'w-screen' || $base === 'w-min' || $base === 'w-max' || $base === 'w-fit'
                || str_starts_with($base, 'w-') => 'width',
            str_starts_with($base, 'max-w-') => 'max-width',
            str_starts_with($base, 'min-w-') => 'min-width',
            $base === 'h-full' || $base === 'h-auto' || $base === 'h-screen' || $base === 'h-min' || $base === 'h-max' || $base === 'h-fit'
                || str_starts_with($base, 'h-') => 'height',
            str_starts_with($base, 'max-h-') => 'max-height',
            str_starts_with($base, 'min-h-') => 'min-height',
            str_starts_with($base, 'overflow-') => 'overflow',
            str_starts_with($base, 'justify-') => 'justify',
            str_starts_with($base, 'items-') => 'items',
            str_starts_with($base, 'self-') => 'self',
            $base === 'flex-row' || $base === 'flex-col' || $base === 'flex-row-reverse' || $base === 'flex-col-reverse' => 'flex-direction',
            $base === 'flex-1' || $base === 'flex-auto' || $base === 'flex-initial' || $base === 'flex-none'
                || $base === 'grow' || $base === 'grow-0' || $base === 'shrink' || $base === 'shrink-0'
                || $base === 'flex-shrink' || $base === 'flex-shrink-0' || $base === 'flex-grow' || $base === 'flex-grow-0' => 'flex-size',
            default => null,
        };
    }
}
