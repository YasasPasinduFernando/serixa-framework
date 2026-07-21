<?php

declare(strict_types=1);

namespace Serixa\Document\Element;

/**
 * HTML &lt;link&gt; element.
 */
final class Link extends HeadElement
{
    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function __construct(
        private readonly array $attributes,
    ) {
        parent::__construct();
    }

    /**
     * @param array<string, string|bool|null> $extra
     */
    public static function make(string $rel, string $href, array $extra = []): self
    {
        return new self(array_merge(['rel' => $rel, 'href' => $href], $extra));
    }

    /**
     * @param array<string, string|bool|null> $extra
     */
    public static function stylesheet(string $href, array $extra = []): self
    {
        return self::make('stylesheet', $href, $extra);
    }

    public static function icon(string $href, string $type = 'image/x-icon'): self
    {
        return self::make('icon', $href, ['type' => $type]);
    }

    public static function canonical(string $href): self
    {
        return self::make('canonical', $href);
    }

    /**
     * @param array<string, string|bool|null> $extra
     */
    public static function preload(string $href, string $as, array $extra = []): self
    {
        return self::make('preload', $href, array_merge(['as' => $as], $extra));
    }

    /**
     * @param array<string, string|bool|null> $extra
     */
    public static function prefetch(string $href, array $extra = []): self
    {
        return self::make('prefetch', $href, $extra);
    }

    public function render(): string
    {
        return '<link' . $this->attrs($this->attributes) . ' />';
    }

    /**
     * @return array<string, string|bool|null>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
}
