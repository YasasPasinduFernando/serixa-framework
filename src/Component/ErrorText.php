<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Error message for a field. Uses role=alert. No validation engine.
 *
 * @example ErrorText::make('Email is required.')->id('email-error')
 */
final class ErrorText extends Component
{
    use ResolvesTheme;

    private string $text;

    private function __construct(string $text)
    {
        parent::__construct();
        $this->text = $text;
    }

    public static function make(string $text): self
    {
        return new self($text);
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('errorText'),
            ['role' => 'alert'],
        );

        return sprintf('<p%s>%s</p>', $attrs, $this->escape($this->text));
    }
}
