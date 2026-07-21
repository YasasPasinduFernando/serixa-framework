<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Dropdown menu item.
 *
 * @example DropdownItem::make('Profile')->href('/profile')
 */
final class DropdownItem extends Component
{
    use ResolvesTheme;

    private string $label;

    private ?string $href = null;

    private bool $disabled = false;

    private bool $danger = false;

    private function __construct(string $label)
    {
        parent::__construct();
        $this->label = $label;
    }

    public static function make(string $label): self
    {
        return new self($label);
    }

    public function href(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function danger(bool $danger = true): self
    {
        $this->danger = $danger;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('dropdownItem', [
                'disabled' => $this->disabled,
                'danger' => $this->danger,
            ]),
            [
                'role' => 'menuitem',
                'data-serixa-dropdown-item' => true,
                'tabindex' => $this->disabled ? '-1' : '0',
                'aria-disabled' => $this->disabled ? 'true' : null,
                'href' => $this->href,
            ],
        );

        $tag = $this->href !== null ? 'a' : 'button';
        $type = $this->href === null ? ' type="button"' : '';
        $disabled = $this->href === null && $this->disabled ? ' disabled' : '';

        return sprintf(
            '<%s%s%s%s>%s</%s>',
            $tag,
            $type,
            $disabled,
            $attrs,
            $this->escape($this->label),
            $tag,
        );
    }
}
