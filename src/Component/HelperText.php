<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Muted helper copy beneath a control. Pair with Input::describedBy($id).
 *
 * @example HelperText::make('We never share your email.')->id('email-help')
 */
final class HelperText extends Component
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
        $attrs = $this->buildAttributeString($this->theme()->classes('helperText'));

        return sprintf('<p%s>%s</p>', $attrs, $this->escape($this->text));
    }
}
