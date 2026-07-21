<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Accessible tooltip (aria-describedby).
 *
 * @example Tooltip::make()->text('More info')->child(Button::make('?')->ghost())
 */
final class Tooltip extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    private string $text = '';

    private WidgetInterface|string|null $child = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function text(string $text): self
    {
        $this->text = $text;

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
        if ($this->child === null) {
            throw new InvalidArgumentException('Tooltip requires child().');
        }

        if ($this->text === '') {
            throw new InvalidArgumentException('Tooltip requires text().');
        }

        $id = $this->resolveWidgetId('tooltip');
        $tipId = $id . '-tip';

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('tooltip'),
            [
                'id' => $id,
                'data-serixa-tooltip' => true,
            ],
        );

        $triggerAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('tooltip.trigger'),
            [
                'data-serixa-tooltip-trigger' => true,
                'aria-describedby' => $tipId,
            ],
        );

        $tipAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('tooltip.content'),
            [
                'id' => $tipId,
                'role' => 'tooltip',
                'data-serixa-tooltip-content' => true,
                'hidden' => true,
            ],
        );

        return sprintf(
            '<span%s><span%s>%s</span><span%s>%s</span></span>',
            $rootAttrs,
            $triggerAttrs,
            $this->renderChild($this->child),
            $tipAttrs,
            $this->escape($this->text),
        );
    }
}
