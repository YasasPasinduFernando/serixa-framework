<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Page title row with optional subtitle and actions.
 *
 * @example PageHeader::make()->title('Users')->actions(Button::make('Create')->primary())
 */
final class PageHeader extends Component
{
    use ResolvesTheme;

    private ?string $title = null;

    private ?string $subtitle = null;

    /** @var list<WidgetInterface|string> */
    private array $actions = [];

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function subtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$actions
     */
    public function actions(WidgetInterface|string|array ...$actions): self
    {
        if (count($actions) === 1 && is_array($actions[0])) {
            /** @var list<WidgetInterface|string> $list */
            $list = array_values($actions[0]);
            $this->actions = $list;
        } else {
            /** @var list<WidgetInterface|string> $list */
            $list = [];

            foreach ($actions as $action) {
                if (is_array($action)) {
                    continue;
                }

                $list[] = $action;
            }

            $this->actions = $list;
        }

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->actions;
    }

    public function render(): string
    {
        $titleBlock = '';

        if ($this->title !== null) {
            $titleBlock .= sprintf(
                '<h1%s>%s</h1>',
                $this->buildAttributeString($this->theme()->classes('pageHeader.title')),
                $this->escape($this->title),
            );
        }

        if ($this->subtitle !== null) {
            $titleBlock .= sprintf(
                '<p%s>%s</p>',
                $this->buildAttributeString($this->theme()->classes('pageHeader.subtitle')),
                $this->escape($this->subtitle),
            );
        }

        $actionsHtml = '';

        if ($this->actions !== []) {
            $actionsHtml = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('pageHeader.actions')),
                $this->renderChildren($this->actions),
            );
        }

        return sprintf(
            '<div%s><div%s>%s</div>%s</div>',
            $this->buildAttributeString($this->theme()->classes('pageHeader')),
            $this->buildAttributeString($this->theme()->classes('pageHeader.text')),
            $titleBlock,
            $actionsHtml,
        );
    }
}
