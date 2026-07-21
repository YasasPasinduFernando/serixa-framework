<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Tab definition (label + panel content).
 *
 * @example Tab::make('General')->content(Text::make('...'))
 */
final class Tab extends Component
{
    use ResolvesTheme;

    private string $label;

    private WidgetInterface|string|null $content = null;

    private bool $active = false;

    private function __construct(string $label)
    {
        parent::__construct();
        $this->label = $label;
    }

    public static function make(string $label): self
    {
        return new self($label);
    }

    public function content(WidgetInterface|string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function active(bool $active = true): self
    {
        $this->active = $active;

        return $this;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function panelContent(): WidgetInterface|string
    {
        if ($this->content === null) {
            throw new InvalidArgumentException('Tab requires content().');
        }

        if ($this->content instanceof TabPanel) {
            return $this->content;
        }

        return $this->content;
    }

    public function childrenForTree(): array
    {
        return $this->content !== null ? [$this->content] : [];
    }

    public function render(): string
    {
        // Tabs renders the full structure; standalone Tab emits a marker for tests.
        return sprintf(
            '<span data-serixa-tab-def>%s</span>',
            $this->escape($this->label),
        );
    }
}
