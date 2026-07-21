<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;

/**
 * Live-region toast (SSR markup; ToastModule manages visibility).
 *
 * @example Toast::make()->message('Saved')->tone('success')
 */
final class Toast extends Component
{
    use AssertsOption;
    use ResolvesTheme;
    use ResolvesWidgetId;

    private string $message = '';

    private string $tone = 'info';

    private bool $open = true;

    private WidgetInterface|string|null $action = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function tone(string $tone): self
    {
        $this->assertOption($tone, ['info', 'success', 'warning', 'danger'], 'tone');
        $this->tone = $tone;

        return $this;
    }

    public function open(bool $open = true): self
    {
        $this->open = $open;

        return $this;
    }

    public function action(WidgetInterface|string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->action !== null ? [$this->action] : [];
    }

    public function render(): string
    {
        $id = $this->resolveWidgetId('toast');

        $action = $this->action !== null
            ? sprintf(
                '<div%s>%s</div>',
                $this->buildStructuralAttributeString($this->theme()->classes('toast.action')),
                $this->renderChild($this->action),
            )
            : '';

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('toast', ['tone' => $this->tone, 'open' => $this->open]),
            [
                'id' => $id,
                'role' => 'status',
                'aria-live' => 'polite',
                'aria-atomic' => 'true',
                'data-serixa-toast' => true,
                'data-serixa-open' => $this->open ? 'true' : 'false',
                'hidden' => $this->open ? null : true,
            ],
        );

        return sprintf(
            '<div%s><p%s>%s</p>%s</div>',
            $attrs,
            $this->buildStructuralAttributeString($this->theme()->classes('toast.message')),
            $this->escape($this->message),
            $action,
        );
    }
}
