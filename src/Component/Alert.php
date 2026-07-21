<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Inline alert / banner (static SSR; optional dismiss metadata).
 *
 * @example Alert::make()->tone('warning')->title('Caution')->body('Check settings')
 */
final class Alert extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $tone = 'info';

    private ?string $title = null;

    private WidgetInterface|string|null $body = null;

    private bool $dismissible = false;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function tone(string $tone): self
    {
        $this->assertOption($tone, ['info', 'success', 'warning', 'danger'], 'tone');
        $this->tone = $tone;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function body(WidgetInterface|string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->body !== null ? [$this->body] : [];
    }

    public function render(): string
    {
        $parts = [];

        if ($this->title !== null) {
            $parts[] = sprintf(
                '<strong%s>%s</strong>',
                $this->buildStructuralAttributeString($this->theme()->classes('alert.title')),
                $this->escape($this->title),
            );
        }

        if ($this->body !== null) {
            $parts[] = sprintf(
                '<div%s>%s</div>',
                $this->buildStructuralAttributeString($this->theme()->classes('alert.body')),
                $this->renderChild($this->body),
            );
        }

        $dismiss = '';

        if ($this->dismissible) {
            $dismiss = sprintf(
                '<button type="button"%s data-serixa-alert-dismiss aria-label="Dismiss">×</button>',
                $this->buildStructuralAttributeString($this->theme()->classes('alert.dismiss')),
            );
        }

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('alert', ['tone' => $this->tone]),
            [
                'role' => 'alert',
                'data-serixa-alert' => true,
            ],
        );

        return sprintf('<div%s>%s%s</div>', $attrs, implode('', $parts), $dismiss);
    }
}
