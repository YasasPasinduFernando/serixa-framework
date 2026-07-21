<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Brand mark: image and/or wordmark text.
 *
 * @example Logo::make()->text('SERIXA')->href('/')
 */
final class Logo extends Component
{
    use ResolvesTheme;

    private ?string $src = null;

    private ?string $alt = null;

    private ?string $text = null;

    private ?string $href = null;

    private string|int|null $width = null;

    private string|int|null $height = null;

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

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function href(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function width(string|int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height(string|int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function childrenForTree(): array
    {
        return [];
    }

    public function render(): string
    {
        $inner = '';

        if ($this->src !== null) {
            $inner .= Image::make()
                ->src($this->src)
                ->alt($this->alt ?? ($this->text ?? 'Logo'))
                ->loading('eager')
                ->width($this->width ?? 32)
                ->height($this->height ?? 32)
                ->class(...$this->theme()->classes('logo.image'))
                ->render();
        }

        if ($this->text !== null) {
            $inner .= sprintf(
                '<span%s>%s</span>',
                $this->buildAttributeString($this->theme()->classes('logo.text')),
                $this->escape($this->text),
            );
        }

        if ($inner === '') {
            $inner = sprintf(
                '<span%s>SERIXA</span>',
                $this->buildAttributeString($this->theme()->classes('logo.text')),
            );
        }

        $attrs = $this->buildAttributeString($this->theme()->classes('logo'));

        if ($this->href !== null) {
            return sprintf(
                '<a%s href="%s">%s</a>',
                $attrs,
                $this->escape($this->href),
                $inner,
            );
        }

        return sprintf('<div%s>%s</div>', $attrs, $inner);
    }
}
