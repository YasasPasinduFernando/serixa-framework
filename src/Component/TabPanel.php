<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Tab panel content (optional composition helper).
 *
 * @example TabPanel::make()->content(Text::make('Body'))
 */
final class TabPanel extends Component
{
    use ResolvesTheme;

    private WidgetInterface|string|null $content = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function content(WidgetInterface|string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->content !== null ? [$this->content] : [];
    }

    public function render(): string
    {
        if ($this->content === null) {
            throw new InvalidArgumentException('TabPanel requires content().');
        }

        return $this->renderChild($this->content);
    }
}
