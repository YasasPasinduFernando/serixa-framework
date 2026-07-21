<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Checkbox with optional visible label.
 *
 * @example Checkbox::make()->name('remember')->label('Remember me')->onChange('toggleRemember')
 */
final class Checkbox extends Component
{
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    private ?string $label = null;

    private bool $checked = false;

    private function __construct()
    {
        parent::__construct();
        $this->value = '1';
    }

    public static function make(): self
    {
        return new self();
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function checked(bool $checked = true): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function render(): string
    {
        $inputId = $this->id ?? ($this->name !== null ? $this->name : ('checkbox-' . spl_object_id($this)));

        $inputAttrs = $this->buildAttributeString(
            $this->theme()->classes('checkbox', [
                'disabled' => $this->disabled,
                'invalid' => $this->invalid,
            ]),
            array_merge($this->formControlAttributes(), $this->domEventAttributes(), [
                'id' => $inputId,
                'type' => 'checkbox',
                'value' => $this->value ?? '1',
                'checked' => $this->checked ? true : null,
            ]),
        );

        $input = sprintf('<input%s />', $inputAttrs);

        if ($this->label === null) {
            return $input;
        }

        $labelAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('checkbox.label'),
            ['for' => $inputId],
        );

        return sprintf(
            '<div%s>%s<label%s>%s</label></div>',
            $this->buildStructuralAttributeString($this->theme()->classes('checkbox.wrap')),
            $input,
            $labelAttrs,
            $this->escape($this->label),
        );
    }
}
