<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Three-slot toolbar: leading / center / trailing.
 *
 * @example Toolbar::make()->leading(SearchField::make())->trailing(Button::make('Create'))
 */
final class Toolbar extends Component
{
    use ResolvesTheme;

    private WidgetInterface|string|null $leading = null;

    private WidgetInterface|string|null $center = null;

    private WidgetInterface|string|null $trailing = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function leading(WidgetInterface|string|null $leading): self
    {
        $this->leading = $leading;

        return $this;
    }

    public function center(WidgetInterface|string|null $center): self
    {
        $this->center = $center;

        return $this;
    }

    public function trailing(WidgetInterface|string|null $trailing): self
    {
        $this->trailing = $trailing;

        return $this;
    }

    public function childrenForTree(): array
    {
        $nodes = [];

        foreach ([$this->leading, $this->center, $this->trailing] as $slot) {
            if ($slot !== null) {
                $nodes[] = $slot;
            }
        }

        return $nodes;
    }

    public function render(): string
    {
        $slots = '';

        foreach (
            [
                'leading' => $this->leading,
                'center' => $this->center,
                'trailing' => $this->trailing,
            ] as $name => $slot
        ) {
            if ($slot === null) {
                continue;
            }

            $slots .= sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('toolbar.' . $name)),
                $this->renderChild($slot),
            );
        }

        return sprintf(
            '<div%s>%s</div>',
            $this->buildAttributeString($this->theme()->classes('toolbar')),
            $slots,
        );
    }
}
