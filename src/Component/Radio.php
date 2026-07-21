<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Exception\InvalidArgumentException;

/**
 * Radio button with optional label.
 *
 * @example Radio::make()->name('plan')->value('pro')->label('Pro')->checked()
 */
final class Radio extends Component
{
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    private ?string $label = null;

    private bool $checked = false;

    private function __construct()
    {
        parent::__construct();
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
        if ($this->value === null) {
            throw new InvalidArgumentException('Radio requires a value().');
        }

        $inputId = $this->id ?? ('radio-' . spl_object_id($this));

        $inputAttrs = $this->buildAttributeString(
            $this->theme()->classes('radio', [
                'disabled' => $this->disabled,
                'invalid' => $this->invalid,
            ]),
            array_merge($this->formControlAttributes(), $this->domEventAttributes(), [
                'id' => $inputId,
                'type' => 'radio',
                'value' => $this->value,
                'checked' => $this->checked ? true : null,
            ]),
        );

        $input = sprintf('<input%s />', $inputAttrs);

        if ($this->label === null) {
            return $input;
        }

        $labelAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('radio.label'),
            ['for' => $inputId],
        );

        return sprintf(
            '<div%s>%s<label%s>%s</label></div>',
            $this->buildStructuralAttributeString($this->theme()->classes('radio.wrap')),
            $input,
            $labelAttrs,
            $this->escape($this->label),
        );
    }
}
