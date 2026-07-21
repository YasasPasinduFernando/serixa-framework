<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Flex-grow wrapper around a single child (Flutter Expanded).
 */
final class Expanded extends Component
{
    use ResolvesTheme;

    private WidgetInterface|string|null $child = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(WidgetInterface|string|null $child = null): self
    {
        $expanded = new self();
        $expanded->child = $child;

        return $expanded;
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
        $attrs = $this->buildAttributeString($this->theme()->classes('expanded'));
        $inner = $this->child !== null ? $this->renderChild($this->child) : '';

        return sprintf('<div%s>%s</div>', $attrs, $inner);
    }
}
