<?php

declare(strict_types=1);

namespace Serixa\Document\Element;

use Serixa\Support\AttributeBag;
use Serixa\Support\HtmlEscaper;

/**
 * Shared escaping / attribute helpers for document head elements.
 *
 * @internal
 */
abstract class HeadElement
{
    protected readonly HtmlEscaper $escaper;

    protected readonly AttributeBag $attributeBag;

    public function __construct(
        ?HtmlEscaper $escaper = null,
        ?AttributeBag $attributeBag = null,
    ) {
        $this->escaper = $escaper ?? new HtmlEscaper();
        $this->attributeBag = $attributeBag ?? new AttributeBag();
    }

    abstract public function render(): string;

    /**
     * @param array<string, string|bool|null> $attributes
     */
    protected function attrs(array $attributes): string
    {
        return $this->attributeBag->build($attributes);
    }

    protected function e(string $value): string
    {
        return $this->escaper->escape($value);
    }
}
