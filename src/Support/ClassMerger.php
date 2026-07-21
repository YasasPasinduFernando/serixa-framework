<?php

declare(strict_types=1);

namespace Serixa\Support;

/**
 * Merges CSS class tokens, preserving order and removing duplicates.
 */
final class ClassMerger
{
    /**
     * @param list<string> ...$groups
     * @return list<string>
     */
    public function merge(array ...$groups): array
    {
        $merged = [];

        foreach ($groups as $group) {
            foreach ($group as $class) {
                $class = trim($class);

                if ($class === '' || isset($merged[$class])) {
                    continue;
                }

                $merged[$class] = true;
            }
        }

        return array_keys($merged);
    }

    /**
     * @param list<string> ...$groups
     */
    public function toString(array ...$groups): string
    {
        return implode(' ', $this->merge(...$groups));
    }
}
