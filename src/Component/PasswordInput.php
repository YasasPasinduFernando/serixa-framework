<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Password field (type=password). No reveal toggle (optional JS is progressive enhancement only).
 *
 * @example PasswordInput::make()->name('password')->required()->minLength(8)
 */
final class PasswordInput extends Component
{
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    private ?string $placeholder = null;

    private string $autocomplete = 'current-password';

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
                'type' => 'password',
                'value' => $this->value,
                'placeholder' => $this->placeholder,
                'autocomplete' => $this->autocomplete,
            ]),
        );

        return sprintf('<input%s />', $attrs);
    }
}
