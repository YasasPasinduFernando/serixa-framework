<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Select list. Options are value => label pairs (render-only).
 *
 * @example Select::make()->name('role')->required()->onChange('filterRole')
 */
final class Select extends Component
{
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    /** @var array<string, string> */
    private array $options = [];

    private ?string $placeholder = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param array<string|int, string> $options
     */
    public function options(array $options): self
    {
        $normalized = [];

        foreach ($options as $value => $label) {
            $normalized[(string) $value] = (string) $label;
        }

        $this->options = $normalized;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('select', [
                'invalid' => $this->invalid,
                'disabled' => $this->disabled,
            ]),
            array_merge($this->formControlAttributes(), $this->domEventAttributes()),
        );

        $optionsHtml = '';

        if ($this->placeholder !== null) {
            $optionsHtml .= sprintf(
                '<option value="" disabled%s>%s</option>',
                $this->value === null || $this->value === '' ? ' selected' : '',
                $this->escape($this->placeholder),
            );
        }

        foreach ($this->options as $optionValue => $label) {
            $selected = $this->value !== null && $this->value === $optionValue ? ' selected' : '';
            $optionsHtml .= sprintf(
                '<option value="%s"%s>%s</option>',
                $this->escape($optionValue),
                $selected,
                $this->escape($label),
            );
        }

        return sprintf('<select%s>%s</select>', $attrs, $optionsHtml);
    }
}
