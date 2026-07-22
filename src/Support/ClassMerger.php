<?php

declare(strict_types=1);

namespace Serixa\Support;

/**
 * Merges CSS class tokens with Tailwind-aware conflict resolution.
 *
 * Exact duplicates are dropped. Within a conflict group (e.g. width utilities),
 * a later token replaces an earlier one so consumer classes can override theme defaults.
 * Variant prefixes (`sm:`, `dark:`, `hover:`, …) scope groups independently.
 *
 * Hot-path caches: per-class conflict groups and identical merge inputs.
 */
final class ClassMerger
{
    /** @var array<string, string|null> */
    private static array $conflictGroupCache = [];

    /** @var array<string, list<string>> */
    private array $mergeCache = [];

    /**
     * @param list<string> ...$groups
     * @return list<string>
     */
    public function merge(array ...$groups): array
    {
        $cacheKey = $this->mergeCacheKey($groups);
        if ($cacheKey !== null && isset($this->mergeCache[$cacheKey])) {
            return $this->mergeCache[$cacheKey];
        }

        /** @var list<string> $ordered */
        $ordered = [];
        /** @var array<string, int> $groupIndex conflict-group => position in $ordered */
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

        $result = array_values($ordered);

        if ($cacheKey !== null && count($this->mergeCache) < 2048) {
            $this->mergeCache[$cacheKey] = $result;
        }

        return $result;
    }

    /**
     * @param list<string> ...$groups
     */
    public function toString(array ...$groups): string
    {
        return implode(' ', $this->merge(...$groups));
    }

    public function clearCache(): void
    {
        $this->mergeCache = [];
    }

    public static function clearConflictGroupCache(): void
    {
        self::$conflictGroupCache = [];
    }

    /**
     * @param list<list<string>> $groups
     */
    private function mergeCacheKey(array $groups): ?string
    {
        $flat = [];
        $count = 0;

        foreach ($groups as $group) {
            foreach ($group as $class) {
                $flat[] = $class;
                ++$count;
                if ($count > 64) {
                    return null;
                }
            }
            $flat[] = "\0";
        }

        return implode("\x1e", $flat);
    }

    private static function conflictGroup(string $class): ?string
    {
        if (array_key_exists($class, self::$conflictGroupCache)) {
            return self::$conflictGroupCache[$class];
        }

        [$variant, $base] = self::splitVariants($class);
        $id = self::baseConflictId($base);
        $group = $id === null ? null : $variant . $id;

        if (count(self::$conflictGroupCache) < 4096) {
            self::$conflictGroupCache[$class] = $group;
        }

        return $group;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function splitVariants(string $class): array
    {
        $variant = '';
        $base = $class;

        while (($pos = strpos($base, ':')) !== false) {
            $before = substr($base, 0, $pos);
            if (str_contains($before, '[')) {
                break;
            }

            $variant .= substr($base, 0, $pos + 1);
            $base = substr($base, $pos + 1);
        }

        return [$variant, $base];
    }

    private static function baseConflictId(string $base): ?string
    {
        if ($base === '') {
            return null;
        }

        if (in_array($base, [
            'block', 'inline-block', 'inline', 'flex', 'inline-flex', 'table', 'inline-table',
            'table-caption', 'table-cell', 'table-column', 'table-column-group', 'table-footer-group',
            'table-header-group', 'table-row-group', 'table-row', 'flow-root', 'grid', 'inline-grid',
            'contents', 'list-item', 'hidden',
        ], true)) {
            return 'display';
        }

        if (in_array($base, ['static', 'fixed', 'absolute', 'relative', 'sticky'], true)) {
            return 'position';
        }

        if (in_array($base, ['flex-row', 'flex-row-reverse', 'flex-col', 'flex-col-reverse'], true)) {
            return 'flex-direction';
        }

        if (in_array($base, ['flex-wrap', 'flex-wrap-reverse', 'flex-nowrap'], true)) {
            return 'flex-wrap';
        }

        if (in_array($base, ['flex-1', 'flex-auto', 'flex-initial', 'flex-none'], true)) {
            return 'flex';
        }

        if ($base === 'grow' || $base === 'grow-0' || str_starts_with($base, 'grow-')
            || $base === 'flex-grow' || $base === 'flex-grow-0') {
            return 'flex-grow';
        }

        if ($base === 'shrink' || $base === 'shrink-0' || str_starts_with($base, 'shrink-')
            || $base === 'flex-shrink' || $base === 'flex-shrink-0') {
            return 'flex-shrink';
        }

        if (str_starts_with($base, 'basis-')) {
            return 'flex-basis';
        }

        if (str_starts_with($base, 'justify-')) {
            return 'justify';
        }

        if (str_starts_with($base, 'items-')) {
            return 'items';
        }

        if (str_starts_with($base, 'content-')) {
            return 'content';
        }

        if (str_starts_with($base, 'self-')) {
            return 'self';
        }

        if (str_starts_with($base, 'place-content-')) {
            return 'place-content';
        }

        if (str_starts_with($base, 'place-items-')) {
            return 'place-items';
        }

        if (str_starts_with($base, 'place-self-')) {
            return 'place-self';
        }

        if ($base === 'order-first' || $base === 'order-last' || $base === 'order-none' || str_starts_with($base, 'order-')) {
            return 'order';
        }

        if (str_starts_with($base, 'grid-cols-')) {
            return 'grid-cols';
        }

        if (str_starts_with($base, 'grid-rows-')) {
            return 'grid-rows';
        }

        if (str_starts_with($base, 'col-span-') || $base === 'col-auto') {
            return 'col-span';
        }

        if (str_starts_with($base, 'row-span-') || $base === 'row-auto') {
            return 'row-span';
        }

        if (str_starts_with($base, 'w-')) {
            return 'width';
        }

        if (str_starts_with($base, 'min-w-')) {
            return 'min-width';
        }

        if (str_starts_with($base, 'max-w-')) {
            return 'max-width';
        }

        if (str_starts_with($base, 'h-')) {
            return 'height';
        }

        if (str_starts_with($base, 'min-h-')) {
            return 'min-height';
        }

        if (str_starts_with($base, 'max-h-')) {
            return 'max-height';
        }

        if (str_starts_with($base, 'gap-x-')) {
            return 'gap-x';
        }

        if (str_starts_with($base, 'gap-y-')) {
            return 'gap-y';
        }

        if (str_starts_with($base, 'gap-')) {
            return 'gap';
        }

        if (str_starts_with($base, 'space-x-') || $base === 'space-x-reverse') {
            return 'space-x';
        }

        if (str_starts_with($base, 'space-y-') || $base === 'space-y-reverse') {
            return 'space-y';
        }

        if ($pad = self::boxSideGroup('p', $base)) {
            return $pad;
        }

        if ($margin = self::boxSideGroup('m', $base)) {
            return $margin;
        }

        if (str_starts_with($base, 'overflow-x-')) {
            return 'overflow-x';
        }

        if (str_starts_with($base, 'overflow-y-')) {
            return 'overflow-y';
        }

        if (str_starts_with($base, 'overflow-')) {
            return 'overflow';
        }

        if ($base === 'rounded' || str_starts_with($base, 'rounded-')) {
            if (preg_match('/^rounded-(t|r|b|l|ss|se|ee|es|tl|tr|br|bl)(-|$)/', $base, $m) === 1) {
                return 'rounded-' . $m[1];
            }

            return 'rounded';
        }

        if ($base === 'shadow' || str_starts_with($base, 'shadow-')) {
            return 'shadow';
        }

        if (str_starts_with($base, 'opacity-') || $base === 'opacity') {
            return 'opacity';
        }

        if (str_starts_with($base, 'cursor-')) {
            return 'cursor';
        }

        if (str_starts_with($base, 'bg-')) {
            return 'background';
        }

        if (in_array($base, ['text-left', 'text-center', 'text-right', 'text-justify', 'text-start', 'text-end'], true)) {
            return 'text-align';
        }

        if (preg_match('/^text-(xs|sm|base|lg|xl|[2-9]?xl)$/', $base) === 1) {
            return 'font-size';
        }

        if (str_starts_with($base, 'text-')) {
            return 'text-color';
        }

        if ($base === 'border' || preg_match('/^border-\d+$/', $base) === 1) {
            return 'border-width';
        }

        if (preg_match('/^border-([trblxy])(-\d+)?$/', $base, $m) === 1) {
            return 'border-width-' . $m[1];
        }

        if (str_starts_with($base, 'border-')
            && ! str_starts_with($base, 'border-collapse')
            && ! str_starts_with($base, 'border-separate')
            && ! str_starts_with($base, 'border-spacing')
            && ! str_starts_with($base, 'border-solid')
            && ! str_starts_with($base, 'border-dashed')
            && ! str_starts_with($base, 'border-dotted')
            && ! str_starts_with($base, 'border-double')
            && ! str_starts_with($base, 'border-none')
            && ! str_starts_with($base, 'border-hidden')) {
            return 'border-color';
        }

        if ($base === 'ring' || $base === 'ring-inset' || preg_match('/^ring-\d+$/', $base) === 1) {
            return 'ring-width';
        }

        if (preg_match('/^ring-offset-\d+$/', $base) === 1) {
            return 'ring-offset-width';
        }

        if (str_starts_with($base, 'ring-offset-')) {
            return 'ring-offset-color';
        }

        if (str_starts_with($base, 'ring-')) {
            return 'ring-color';
        }

        return null;
    }

    private static function boxSideGroup(string $prefix, string $base): ?string
    {
        if ($base === $prefix || preg_match('/^' . preg_quote($prefix, '/') . '-(\d|px|auto|\[)/', $base) === 1) {
            return $prefix;
        }

        foreach (['x', 'y', 't', 'r', 'b', 'l', 's', 'e'] as $side) {
            $token = $prefix . $side;
            if ($base === $token || str_starts_with($base, $token . '-')) {
                return $token;
            }
        }

        return null;
    }
}
