<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Interactive button. Semantic props map to Tailwind via the active theme.
 *
 * @example Button::make('Save')->primary()->large()->rounded()->onClick('saveForm')
 */
final class Button extends Component
{
    use AssertsOption;
    use HasDomEvents;
    use ResolvesTheme;

    private string $label;

    private string $type = 'button';

    private string $variant = 'primary';

    private string $size = 'md';

    private bool $disabled = false;

    private bool $rounded = false;

    private function __construct(string $label)
    {
        parent::__construct();
        $this->label = $label;
    }

    public static function make(string $label): self
    {
        return new self($label);
    }

    public function type(string $type): self
    {
        $this->assertOption($type, ['button', 'submit', 'reset'], 'type');
        $this->type = $type;

        return $this;
    }

    public function variant(string $variant): self
    {
        $this->assertOption($variant, ['primary', 'secondary', 'danger', 'ghost'], 'variant');
        $this->variant = $variant;

        return $this;
    }

    public function primary(): self
    {
        return $this->variant('primary');
    }

    public function secondary(): self
    {
        return $this->variant('secondary');
    }

    public function danger(): self
    {
        return $this->variant('danger');
    }

    public function ghost(): self
    {
        return $this->variant('ghost');
    }

    public function size(string $size): self
    {
        $this->assertOption($size, ['sm', 'md', 'lg'], 'size');
        $this->size = $size;

        return $this;
    }

    public function small(): self
    {
        return $this->size('sm');
    }

    public function large(): self
    {
        return $this->size('lg');
    }

    public function rounded(bool $rounded = true): self
    {
        $this->rounded = $rounded;

        return $this;
    }

    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('button', [
                'variant' => $this->variant,
                'size' => $this->size,
                'disabled' => $this->disabled,
                'rounded' => $this->rounded,
            ]),
            array_merge($this->domEventAttributes(), [
                'type' => $this->type,
                'disabled' => $this->disabled ? true : null,
                'aria-disabled' => $this->disabled ? 'true' : null,
            ]),
        );

        return sprintf(
            '<button%s>%s</button>',
            $attrs,
            $this->escape($this->label),
        );
    }
}
