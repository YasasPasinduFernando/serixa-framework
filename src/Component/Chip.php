<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Removable / selectable chip (render + optional dismiss metadata).
 *
 * @example Chip::make('PHP')->dismissible()
 */
final class Chip extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $label;

    private string $color = 'neutral';

    private bool $dismissible = false;

    private WidgetInterface|string|null $icon = null;

    private function __construct(string $label)
    {
        parent::__construct();
        $this->label = $label;
    }

    public static function make(string $label): self
    {
        return new self($label);
    }

    public function color(string $color): self
    {
        $this->assertOption($color, ['neutral', 'primary', 'success', 'warning', 'danger'], 'color');
        $this->color = $color;

        return $this;
    }

    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    public function icon(WidgetInterface|string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->icon !== null ? [$this->icon] : [];
    }

    public function render(): string
    {
        $icon = $this->icon !== null
            ? sprintf('<span aria-hidden="true">%s</span>', $this->renderChild($this->icon))
            : '';

        $dismiss = '';

        if ($this->dismissible) {
            $dismiss = sprintf(
                '<button type="button"%s data-serixa-chip-dismiss aria-label="Remove">×</button>',
                $this->buildStructuralAttributeString($this->theme()->classes('chip.dismiss')),
            );
        }

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('chip', ['color' => $this->color]),
            ['data-serixa-chip' => true],
        );

        return sprintf(
            '<span%s>%s<span%s>%s</span>%s</span>',
            $attrs,
            $icon,
            $this->buildStructuralAttributeString($this->theme()->classes('chip.label')),
            $this->escape($this->label),
            $dismiss,
        );
    }
}
