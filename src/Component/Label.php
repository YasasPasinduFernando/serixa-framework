<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Label associated with a control via for/id.
 *
 * @example Label::make('Email')->for('email')
 */
final class Label extends Component
{
    use ResolvesTheme;

    private string $text;

    private ?string $forId = null;

    private function __construct(string $text)
    {
        parent::__construct();
        $this->text = $text;
    }

    public static function make(string $text): self
    {
        return new self($text);
    }

    public function for(string $id): self
    {
        $this->forId = $id;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('label'),
            ['for' => $this->forId],
        );

        return sprintf('<label%s>%s</label>', $attrs, $this->escape($this->text));
    }
}
