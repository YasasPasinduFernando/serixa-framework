<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Compact inline metric (label + value).
 *
 * @example Metric::make()->label('Latency')->value('42ms')
 */
final class Metric extends Component
{
    use ResolvesTheme;

    private ?string $label = null;

    private ?string $value = null;

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

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function render(): string
    {
        $labelHtml = $this->label !== null
            ? sprintf(
                '<dt%s>%s</dt>',
                $this->buildAttributeString($this->theme()->classes('metric.label')),
                $this->escape($this->label),
            )
            : '';

        $valueHtml = sprintf(
            '<dd%s>%s</dd>',
            $this->buildAttributeString($this->theme()->classes('metric.value')),
            $this->escape($this->value ?? ''),
        );

        return sprintf(
            '<dl%s>%s%s</dl>',
            $this->buildAttributeString($this->theme()->classes('metric')),
            $labelHtml,
            $valueHtml,
        );
    }
}
