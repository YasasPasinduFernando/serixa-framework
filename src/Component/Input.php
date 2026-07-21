<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Textual HTML input. No validation engine — aria-invalid is UI-only.
 *
 * @example Input::make()->id('email')->name('email')->type('email')->onInput('search')
 */
final class Input extends Component
{
    use AssertsOption;
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    private string $type = 'text';

    private ?string $placeholder = null;

    private ?string $autocomplete = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function type(string $type): self
    {
        $this->assertOption($type, [
            'text', 'email', 'search', 'tel', 'url', 'number', 'date',
            'datetime-local', 'month', 'week', 'time', 'password', 'hidden',
        ], 'type');
        $this->type = $type;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function autocomplete(string $autocomplete): self
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('input', [
                'invalid' => $this->invalid,
                'disabled' => $this->disabled,
            ]),
            array_merge($this->formControlAttributes(), $this->domEventAttributes(), [
                'type' => $this->type,
                'value' => $this->value,
                'placeholder' => $this->placeholder,
                'autocomplete' => $this->autocomplete,
            ]),
        );

        return sprintf('<input%s />', $attrs);
    }
}
