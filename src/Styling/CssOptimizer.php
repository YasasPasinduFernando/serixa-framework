<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * Deduplicates and merges compiled style rules.
 */
final class CssOptimizer
{
    /**
     * @param list<StyleRule> $rules
     * @return list<StyleRule>
     */
    public function optimize(array $rules): array
    {
        /** @var array<string, StyleRule> $bySelector */
        $bySelector = [];

        foreach ($rules as $rule) {
            $key = $rule->selector()->value();
            if (isset($bySelector[$key])) {
                $bySelector[$key] = $bySelector[$key]->mergeProperties($rule->properties());
            } else {
                $bySelector[$key] = $rule;
            }
        }

        // Drop empty rules and identical property bags that share a selector (already merged).
        $optimized = [];
        foreach ($bySelector as $rule) {
            if ($rule->properties() === []) {
                continue;
            }
            $optimized[] = $rule;
        }

        return $optimized;
    }

    /**
     * Collapse consecutive identical CSS chunks (e.g. duplicated foundation files).
     */
    public function dedupeCssChunks(string $css): string
    {
        $parts = preg_split('/\n(?=\/\* --- )/', $css) ?: [$css];
        $seen = [];
        $out = [];

        foreach ($parts as $part) {
            $hash = hash('sha256', trim($part));
            if (isset($seen[$hash])) {
                continue;
            }
            $seen[$hash] = true;
            $out[] = $part;
        }

        return implode("\n", $out);
    }
}
