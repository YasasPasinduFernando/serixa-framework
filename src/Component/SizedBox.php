<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Explicit width/height box (Flutter SizedBox).
 *
 * Static height()/width() factories match Flutter DX.
 * Chain the other axis with withWidth() / withHeight() (PHP cannot overload static + instance names).
 *
 * @example SizedBox::height(20)
 * @example SizedBox::width(32)->withHeight(32)
 */
final class SizedBox extends Component
{
    use ResolvesTheme;

    private int|float|string|null $boxWidth = null;

    private int|float|string|null $boxHeight = null;

    private WidgetInterface|string|null $child = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(WidgetInterface|string|null $child = null): self
    {
        $box = new self();
        $box->child = $child;

        return $box;
    }

    public static function width(int|float|string $width, WidgetInterface|string|null $child = null): self
    {
        return self::make($child)->withWidth($width);
    }

    public static function height(int|float|string $height, WidgetInterface|string|null $child = null): self
    {
        return self::make($child)->withHeight($height);
    }

    public function withWidth(int|float|string $width): self
    {
        $this->boxWidth = $width;

        return $this;
    }

    public function withHeight(int|float|string $height): self
    {
        $this->boxHeight = $height;

        return $this;
    }

    public function child(WidgetInterface|string $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->child !== null ? [$this->child] : [];
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('sizedBox', [
                'width' => $this->boxWidth,
                'height' => $this->boxHeight,
            ]),
        );

        $inner = $this->child !== null ? $this->renderChild($this->child) : '';

        return sprintf('<div%s>%s</div>', $attrs, $inner);
    }
}
