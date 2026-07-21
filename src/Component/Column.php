<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Vertical flex layout (Flutter Column).
 *
 * @example Column::make([Text::make('A'), SizedBox::height(8), Text::make('B')])->gap(4)
 */
final class Column extends Component
{
    use HasChildren;
    use ResolvesTheme;

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
        $column = new self();
        $column->setChildren(self::normalizeChildren($children));

        return $column;
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
            $this->theme()->classes('column', [
                'align' => $this->align,
                'gap' => $this->gap,
            ]),
        );

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
