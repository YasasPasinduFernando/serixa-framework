<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * User avatar with image or initials fallback.
 *
 * @example Avatar::make()->initials('AL')->size('md')->circle()
 */
final class Avatar extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private ?string $src = null;

    private ?string $alt = null;

    private ?string $initials = null;

    private string $size = 'md';

    private string $shape = 'circle';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function image(string $src, ?string $alt = null): self
    {
        $this->src = $src;
        $this->alt = $alt;

        return $this;
    }

    public function initials(string $initials): self
    {
        $this->initials = mb_strtoupper(mb_substr(trim($initials), 0, 3));

        return $this;
    }

    public function size(string $size): self
    {
        $this->assertOption($size, ['sm', 'md', 'lg', 'xl'], 'size');
        $this->size = $size;

        return $this;
    }

    public function circle(bool $circle = true): self
    {
        $this->shape = $circle ? 'circle' : 'square';

        return $this;
    }

    public function square(bool $square = true): self
    {
        $this->shape = $square ? 'square' : 'circle';

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('avatar', [
                'size' => $this->size,
                'shape' => $this->shape,
            ]),
            ['role' => 'img', 'aria-label' => $this->alt ?? $this->initials ?? 'Avatar'],
        );

        if ($this->src !== null && $this->src !== '') {
            $imgAttrs = $this->buildAttributeString(
                $this->theme()->classes('avatar.image'),
                [
                    'src' => $this->src,
                    'alt' => $this->alt ?? '',
                ],
            );

            return sprintf('<span%s><img%s /></span>', $attrs, $imgAttrs);
        }

        return sprintf(
            '<span%s><span%s>%s</span></span>',
            $attrs,
            $this->buildAttributeString($this->theme()->classes('avatar.initials')),
            $this->escape($this->initials ?? '?'),
        );
    }
}
