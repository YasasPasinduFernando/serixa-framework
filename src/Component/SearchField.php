<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Render-only search input. No search logic.
 *
 * @example SearchField::make()->name('q')->onInput('search')
 */
final class SearchField extends Component
{
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    private string $placeholder = 'Search…';

    private WidgetInterface|string|null $icon = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function icon(WidgetInterface|string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->icon !== null ? [$this->icon] : [];
    }

    public function render(): string
    {
        $inputAttrs = $this->buildAttributeString(
            $this->theme()->classes('searchField.input', ['disabled' => $this->disabled]),
            array_merge($this->formControlAttributes(), $this->domEventAttributes(), [
                'type' => 'search',
                'value' => $this->value,
                'placeholder' => $this->placeholder,
                'role' => 'searchbox',
            ]),
        );

        $iconHtml = '';

        if ($this->icon !== null) {
            $iconHtml = sprintf(
                '<span%s aria-hidden="true">%s</span>',
                $this->buildStructuralAttributeString($this->theme()->classes('searchField.icon')),
                $this->renderChild($this->icon),
            );
        }

        return sprintf(
            '<div%s>%s<input%s /></div>',
            $this->buildStructuralAttributeString(
                $this->theme()->classes('searchField'),
                ['role' => 'search'],
            ),
            $iconHtml,
            $inputAttrs,
        );
    }
}
