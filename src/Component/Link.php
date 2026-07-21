<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Anchor navigation primitive.
 *
 * @example Link::make('Dashboard')->href('/')->active()
 */
final class Link extends Component
{
    use ResolvesTheme;

    private string $label;

    private string $href = '#';

    private ?string $target = null;

    private ?string $rel = null;

    private bool $active = false;

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

    public function target(string $target): self
    {
        $this->target = $target;

        if ($target === '_blank' && $this->rel === null) {
            $this->rel = 'noopener noreferrer';
        }

        return $this;
    }

    public function rel(string $rel): self
    {
        $this->rel = $rel;

        return $this;
    }

    public function active(bool $active = true): self
    {
        $this->active = $active;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('link', ['active' => $this->active]),
            [
                'href' => $this->href,
                'target' => $this->target,
                'rel' => $this->rel,
                'aria-current' => $this->active ? 'page' : null,
            ],
        );

        return sprintf('<a%s>%s</a>', $attrs, $this->escape($this->label));
    }
}
