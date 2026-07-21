<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Lightweight application shell. Purely structural — no routing or auth.
 *
 * @example Scaffold::make()->appBar($bar)->sidebar($nav)->body($page)->footer($foot)
 */
final class Scaffold extends Component
{
    use ResolvesTheme;

    private WidgetInterface|string|null $appBar = null;

    private WidgetInterface|string|null $sidebar = null;

    private WidgetInterface|string|null $body = null;

    private WidgetInterface|string|null $footer = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function appBar(WidgetInterface|string|null $appBar): self
    {
        $this->appBar = $appBar;

        return $this;
    }

    public function sidebar(WidgetInterface|string|null $sidebar): self
    {
        $this->sidebar = $sidebar;

        return $this;
    }

    public function body(WidgetInterface|string|null $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Alias of {@see body()} for app-shell readability.
     */
    public function content(WidgetInterface|string|null $content): self
    {
        return $this->body($content);
    }

    public function footer(WidgetInterface|string|null $footer): self
    {
        $this->footer = $footer;

        return $this;
    }

    public function childrenForTree(): array
    {
        $children = [];

        foreach ([$this->appBar, $this->sidebar, $this->body, $this->footer] as $slot) {
            if ($slot !== null) {
                $children[] = $slot;
            }
        }

        return $children;
    }

    public function render(): string
    {
        $parts = [];

        if ($this->appBar !== null) {
            $parts[] = sprintf(
                '<header%s>%s</header>',
                $this->buildAttributeString($this->theme()->classes('scaffold.appBar')),
                $this->renderChild($this->appBar),
            );
        }

        $mainInner = '';

        if ($this->sidebar !== null) {
            $mainInner .= sprintf(
                '<aside%s>%s</aside>',
                $this->buildAttributeString($this->theme()->classes('scaffold.sidebar')),
                $this->renderChild($this->sidebar),
            );
        }

        $mainInner .= sprintf(
            '<div%s>%s</div>',
            $this->buildAttributeString($this->theme()->classes('scaffold.body')),
            $this->body !== null ? $this->renderChild($this->body) : '',
        );

        $parts[] = sprintf(
            '<div%s>%s</div>',
            $this->buildAttributeString($this->theme()->classes('scaffold.main')),
            $mainInner,
        );

        if ($this->footer !== null) {
            $parts[] = sprintf(
                '<footer%s>%s</footer>',
                $this->buildAttributeString($this->theme()->classes('scaffold.footer')),
                $this->renderChild($this->footer),
            );
        }

        $attrs = $this->buildAttributeString($this->theme()->classes('scaffold'));

        return sprintf('<div%s>%s</div>', $attrs, implode('', $parts));
    }
}
