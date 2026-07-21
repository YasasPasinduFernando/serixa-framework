<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Exception\InvalidArgumentException;

/**
 * Semantic image element.
 *
 * @example Image::make()->src('/hero.jpg')->alt('Hero')->loading('lazy')
 */
final class Image extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private ?string $src = null;

    private string $alt = '';

    private string|int|null $width = null;

    private string|int|null $height = null;

    private string $loading = 'lazy';

    private string $decoding = 'async';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function src(string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function alt(string $alt): self
    {
        $this->alt = $alt;

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

    public function loading(string $loading): self
    {
        $this->assertOption($loading, ['lazy', 'eager'], 'loading');
        $this->loading = $loading;

        return $this;
    }

    public function decoding(string $decoding): self
    {
        $this->assertOption($decoding, ['async', 'sync', 'auto'], 'decoding');
        $this->decoding = $decoding;

        return $this;
    }

    public function render(): string
    {
        if ($this->src === null || $this->src === '') {
            throw new InvalidArgumentException('Image requires src().');
        }

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('image'),
            [
                'src' => $this->src,
                'alt' => $this->alt,
                'width' => $this->width !== null ? (string) $this->width : null,
                'height' => $this->height !== null ? (string) $this->height : null,
                'loading' => $this->loading,
                'decoding' => $this->decoding,
            ],
        );

        return sprintf('<img%s />', $attrs);
    }
}
