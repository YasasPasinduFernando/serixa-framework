<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Top application bar content for Scaffold::appBar() (Scaffold owns the &lt;header&gt;).
 *
 * @example AppBar::make()->title('Dashboard')->actions(Button::make('Logout')->ghost())
 */
final class AppBar extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private ?string $title = null;

    /** @var list<WidgetInterface|string> */
    private array $actions = [];

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $bar = new self();
        $bar->setChildren(self::normalizeChildren($children));

        return $bar;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$actions
     */
    public function actions(WidgetInterface|string|array ...$actions): self
    {
        $this->actions = self::normalizeChildren($actions);

        return $this;
    }

    public function childrenForTree(): array
    {
        $nodes = $this->getChildren();

        foreach ($this->actions as $action) {
            $nodes[] = $action;
        }

        return $nodes;
    }

    public function render(): string
    {
        $titleHtml = '';

        if ($this->title !== null) {
            $titleHtml = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('appBar.title')),
                $this->escape($this->title),
            );
        }

        $actionsHtml = '';

        if ($this->actions !== []) {
            $actionsHtml = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('appBar.actions')),
                $this->renderChildren($this->actions),
            );
        }

        $attrs = $this->buildAttributeString($this->theme()->classes('appBar'));

        return sprintf(
            '<div%s>%s%s%s</div>',
            $attrs,
            $titleHtml,
            $this->renderChildren($this->getChildren()),
            $actionsHtml,
        );
    }
}
