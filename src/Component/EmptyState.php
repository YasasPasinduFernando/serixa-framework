<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Empty collection / zero-results placeholder.
 *
 * @example EmptyState::make()->title('No users')->action(Button::make('Create')->primary())
 */
final class EmptyState extends Component
{
    use ResolvesTheme;

    private WidgetInterface|string|null $icon = null;

    private ?string $title = null;

    private ?string $description = null;

    private WidgetInterface|string|null $action = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function icon(WidgetInterface|string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function action(WidgetInterface|string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function childrenForTree(): array
    {
        $nodes = [];

        if ($this->icon !== null) {
            $nodes[] = $this->icon;
        }

        if ($this->action !== null) {
            $nodes[] = $this->action;
        }

        return $nodes;
    }

    public function render(): string
    {
        $parts = [];

        if ($this->icon !== null) {
            $parts[] = sprintf(
                '<div%s aria-hidden="true">%s</div>',
                $this->buildAttributeString($this->theme()->classes('emptyState.icon')),
                $this->renderChild($this->icon),
            );
        }

        if ($this->title !== null) {
            $parts[] = sprintf(
                '<h3%s>%s</h3>',
                $this->buildAttributeString($this->theme()->classes('emptyState.title')),
                $this->escape($this->title),
            );
        }

        if ($this->description !== null) {
            $parts[] = sprintf(
                '<p%s>%s</p>',
                $this->buildAttributeString($this->theme()->classes('emptyState.description')),
                $this->escape($this->description),
            );
        }

        if ($this->action !== null) {
            $parts[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('emptyState.action')),
                $this->renderChild($this->action),
            );
        }

        return sprintf(
            '<div%s>%s</div>',
            $this->buildAttributeString($this->theme()->classes('emptyState')),
            implode('', $parts),
        );
    }
}
