<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\HasFormControl;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Multi-line text control.
 *
 * @example Textarea::make()->name('bio')->rows(4)->maxLength(500)
 */
final class Textarea extends Component
{
    use HasDomEvents;
    use HasFormControl;
    use ResolvesTheme;

    private ?string $placeholder = null;

    private int $rows = 3;

    private ?int $cols = null;

    private string $content = '';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(string $content = ''): self
    {
        $textarea = new self();
        $textarea->content = $content;

        return $textarea;
    }

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function rows(int $rows): self
    {
        $this->rows = max(1, $rows);

        return $this;
    }

    public function cols(int $cols): self
    {
        $this->cols = max(1, $cols);

        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('textarea', [
                'invalid' => $this->invalid,
                'disabled' => $this->disabled,
            ]),
            array_merge($this->formControlAttributes(), $this->domEventAttributes(), [
                'placeholder' => $this->placeholder,
                'rows' => (string) $this->rows,
                'cols' => $this->cols !== null ? (string) $this->cols : null,
            ]),
        );

        return sprintf('<textarea%s>%s</textarea>', $attrs, $this->escape($this->content));
    }
}
