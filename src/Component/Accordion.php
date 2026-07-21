<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Exception\InvalidArgumentException;

/**
 * Accordion list (SSR + AccordionModule).
 *
 * @example Accordion::make()->item(AccordionItem::make()->title('FAQ')->content(...))
 */
final class Accordion extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    /** @var list<AccordionItem> */
    private array $items = [];

    private bool $multiple = false;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function item(AccordionItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->items;
    }

    public function render(): string
    {
        if ($this->items === []) {
            throw new InvalidArgumentException('Accordion requires at least one item().');
        }

        $id = $this->resolveWidgetId('accordion');
        $parts = [];

        foreach ($this->items as $index => $item) {
            $itemId = $id . '-item-' . $index;
            $parts[] = $item->renderWithIds($itemId, $itemId . '-panel', $itemId . '-trigger');
        }

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('accordion'),
            [
                'id' => $id,
                'data-serixa-accordion' => true,
                'data-serixa-multiple' => $this->multiple ? 'true' : 'false',
            ],
        );

        return sprintf('<div%s>%s</div>', $attrs, implode('', $parts));
    }
}
