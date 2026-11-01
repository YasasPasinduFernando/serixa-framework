<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Widget\Widget;

/**
 * Anchored popover panel.
 *
 * @example Popover::make()->trigger(Button::make('More'))->content(Text::make('Details'))
 */
final class Popover extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    private WidgetInterface|string|null $trigger = null;

    private WidgetInterface|string|null $content = null;

    private bool $open = false;

    private ?string $ariaLabel = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function trigger(WidgetInterface|string $trigger): self
    {
        $this->trigger = $trigger;

        return $this;
    }

    public function content(WidgetInterface|string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function open(bool $open = true): self
    {
        $this->open = $open;

        return $this;
    }

    public function ariaLabel(string $label): self
    {
        $this->ariaLabel = $label;

        return $this;
    }

    public function childrenForTree(): array
    {
        $nodes = [];

        if ($this->trigger !== null) {
            $nodes[] = $this->trigger;
        }

        if ($this->content !== null) {
            $nodes[] = $this->content;
        }

        return $nodes;
    }

    public function render(): string
    {
        if ($this->trigger === null || $this->content === null) {
            throw new InvalidArgumentException(
                'Popover requires trigger() and content(). Example: Popover::make()->trigger(Button::make("More"))->content(Text::make("Details")).',
            );
        }

        $id = $this->resolveWidgetId('popover');
        $panelId = $id . '-panel';

        $triggerAttributes = [
            'data-serixa-popover-trigger' => true,
            'aria-haspopup' => 'dialog',
            'aria-expanded' => $this->open ? 'true' : 'false',
            'aria-controls' => $panelId,
        ];

        if ($this->trigger instanceof Widget) {
            $trigger = clone $this->trigger;
            $triggerWrap = $trigger->attributes($triggerAttributes)->render();
        } else {
            $triggerWrap = sprintf(
                '<button%s type="button">%s</button>',
                $this->buildStructuralAttributeString($this->theme()->classes('popover.trigger'), $triggerAttributes),
                $this->renderChild($this->trigger),
            );
        }

        $panelAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('popover.panel', ['open' => $this->open]),
            [
                'id' => $panelId,
                'role' => 'dialog',
                'aria-label' => $this->ariaLabel ?? 'Popover',
                'data-serixa-popover-panel' => true,
                'hidden' => $this->open ? null : true,
            ],
        );

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('popover'),
            [
                'id' => $id,
                'data-serixa-popover' => true,
                'data-serixa-open' => $this->open ? 'true' : 'false',
            ],
        );

        return sprintf(
            '<div%s>%s<div%s>%s</div></div>',
            $rootAttrs,
            $triggerWrap,
            $panelAttrs,
            $this->renderChild($this->content),
        );
    }
}
