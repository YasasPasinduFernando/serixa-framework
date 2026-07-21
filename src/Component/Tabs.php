<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Exception\InvalidArgumentException;

/**
 * Tablist + panels (SSR + TabsModule).
 *
 * @example Tabs::make()->tab(Tab::make('General')->content(...))->tab(Tab::make('Security')->content(...))
 */
final class Tabs extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    /** @var list<Tab> */
    private array $tabs = [];

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function tab(Tab $tab): self
    {
        $this->tabs[] = $tab;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->tabs;
    }

    public function render(): string
    {
        if ($this->tabs === []) {
            throw new InvalidArgumentException('Tabs requires at least one tab().');
        }

        $id = $this->resolveWidgetId('tabs');
        $activeIndex = 0;

        foreach ($this->tabs as $index => $tab) {
            if ($tab->isActive()) {
                $activeIndex = $index;
                break;
            }
        }

        $buttons = [];
        $panels = [];

        foreach ($this->tabs as $index => $tab) {
            $tabId = $id . '-tab-' . $index;
            $panelId = $id . '-panel-' . $index;
            $selected = $index === $activeIndex;

            $buttons[] = sprintf(
                '<button%s type="button" role="tab" id="%s" aria-selected="%s" aria-controls="%s" tabindex="%s" data-serixa-tab>%s</button>',
                $this->buildStructuralAttributeString($this->theme()->classes('tabs.tab', ['active' => $selected])),
                $this->escape($tabId),
                $selected ? 'true' : 'false',
                $this->escape($panelId),
                $selected ? '0' : '-1',
                $this->escape($tab->label()),
            );

            $panels[] = sprintf(
                '<div%s role="tabpanel" id="%s" aria-labelledby="%s" data-serixa-tabpanel%s>%s</div>',
                $this->buildStructuralAttributeString($this->theme()->classes('tabs.panel', ['active' => $selected])),
                $this->escape($panelId),
                $this->escape($tabId),
                $selected ? '' : ' hidden',
                $this->renderChild($tab->panelContent()),
            );
        }

        $listAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('tabs.list'),
            ['role' => 'tablist', 'data-serixa-tablist' => true],
        );

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('tabs'),
            [
                'id' => $id,
                'data-serixa-tabs' => true,
            ],
        );

        return sprintf(
            '<div%s><div%s>%s</div>%s</div>',
            $rootAttrs,
            $listAttrs,
            implode('', $buttons),
            implode('', $panels),
        );
    }
}
