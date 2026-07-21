<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Horizontal flex layout (Flutter Row).
 *
 * @example Row::make([Button::make('A'), Spacer::make(), Button::make('B')])->spaceBetween()
 */
final class Row extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private string $justify = 'start';

    private string $align = 'stretch';

    private int|float|string|null $gap = null;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $row = new self();
        $row->setChildren(self::normalizeChildren($children));

        return $row;
    }

    public function justifyStart(): self
    {
        $this->justify = 'start';

        return $this;
    }

    public function justifyCenter(): self
    {
        $this->justify = 'center';

        return $this;
    }

    public function justifyEnd(): self
    {
        $this->justify = 'end';

        return $this;
    }

    public function spaceBetween(): self
    {
        $this->justify = 'between';

        return $this;
    }

    public function spaceAround(): self
    {
        $this->justify = 'around';

        return $this;
    }

    public function alignStart(): self
    {
        $this->align = 'start';

        return $this;
    }

    public function alignCenter(): self
    {
        $this->align = 'center';

        return $this;
    }

    public function alignEnd(): self
    {
        $this->align = 'end';

        return $this;
    }

    public function alignStretch(): self
    {
        $this->align = 'stretch';

        return $this;
    }

    public function gap(int|float|string $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('row', [
                'justify' => $this->justify,
                'align' => $this->align,
                'gap' => $this->gap,
            ]),
        );

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
