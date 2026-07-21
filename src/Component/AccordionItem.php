<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Accordion section.
 *
 * @example AccordionItem::make()->title('FAQ')->content(Text::make('Answer'))
 */
final class AccordionItem extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    private ?string $title = null;

    private WidgetInterface|string|null $content = null;

    private bool $open = false;

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

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function childrenForTree(): array
    {
        return $this->content !== null ? [$this->content] : [];
    }

    /**
     * @internal Rendered by Accordion with shared ids.
     */
    public function renderWithIds(string $itemId, string $panelId, string $buttonId): string
    {
        if ($this->title === null || $this->content === null) {
            throw new InvalidArgumentException('AccordionItem requires title() and content().');
        }

        $buttonAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('accordion.button'),
            [
                'type' => 'button',
                'id' => $buttonId,
                'aria-expanded' => $this->open ? 'true' : 'false',
                'aria-controls' => $panelId,
                'data-serixa-accordion-trigger' => true,
            ],
        );

        $panelAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('accordion.panel', ['open' => $this->open]),
            [
                'id' => $panelId,
                'role' => 'region',
                'aria-labelledby' => $buttonId,
                'data-serixa-accordion-panel' => true,
                'hidden' => $this->open ? null : true,
            ],
        );

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('accordionItem', ['open' => $this->open]),
            [
                'id' => $itemId,
                'data-serixa-accordion-item' => true,
                'data-serixa-open' => $this->open ? 'true' : 'false',
            ],
        );

        return sprintf(
            '<div%s><h3%s><button%s>%s</button></h3><div%s>%s</div></div>',
            $rootAttrs,
            $this->buildStructuralAttributeString($this->theme()->classes('accordion.heading')),
            $buttonAttrs,
            $this->escape($this->title),
            $panelAttrs,
            $this->renderChild($this->content),
        );
    }

    public function render(): string
    {
        $id = $this->resolveWidgetId('accordion-item');

        return $this->renderWithIds($id, $id . '-panel', $id . '-trigger');
    }
}
