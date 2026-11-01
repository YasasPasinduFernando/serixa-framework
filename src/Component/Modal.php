<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;

/**
 * Dialog overlay (SSR + ModalModule for focus trap / Escape).
 *
 * @example Modal::make()->title('Delete')->body(...)->footer(...)->open(false)
 */
final class Modal extends Component
{
    use ResolvesTheme;
    use ResolvesWidgetId;

    private ?string $title = null;

    private WidgetInterface|string|null $body = null;

    /** @var list<WidgetInterface|string> */
    private array $footer = [];

    private bool $open = false;

    private bool $dismissible = true;

    private ?string $ariaLabel = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
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

    /**
     * @param WidgetInterface|string ...$footer
     */
    public function footer(WidgetInterface|string ...$footer): self
    {
        $this->footer = array_values($footer);

        return $this;
    }

    public function open(bool $open = true): self
    {
        $this->open = $open;

        return $this;
    }

    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    public function ariaLabel(string $label): self
    {
        $this->ariaLabel = $label;

        return $this;
    }

    public function childrenForTree(): array
    {
        $nodes = [];

        if ($this->body !== null) {
            $nodes[] = $this->body;
        }

        return array_merge($nodes, $this->footer);
    }

    public function render(): string
    {
        $id = $this->resolveWidgetId('modal');
        $titleId = $id . '-title';

        $titleHtml = $this->title !== null
            ? sprintf(
                '<h2%s id="%s">%s</h2>',
                $this->buildStructuralAttributeString($this->theme()->classes('modal.title')),
                $this->escape($titleId),
                $this->escape($this->title),
            )
            : '';

        $bodyHtml = $this->body !== null
            ? sprintf(
                '<div%s>%s</div>',
                $this->buildStructuralAttributeString($this->theme()->classes('modal.body')),
                $this->renderChild($this->body),
            )
            : '';

        $footerHtml = $this->footer !== []
            ? sprintf(
                '<div%s>%s</div>',
                $this->buildStructuralAttributeString($this->theme()->classes('modal.footer')),
                $this->renderChildren($this->footer),
            )
            : '';

        $close = $this->dismissible
            ? sprintf(
                '<button type="button"%s data-serixa-close aria-label="Close">×</button>',
                $this->buildStructuralAttributeString($this->theme()->classes('modal.close')),
            )
            : '';

        $dialogAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('modal.dialog'),
            [
                'role' => 'dialog',
                'aria-modal' => 'true',
                'aria-labelledby' => $this->title !== null ? $titleId : null,
                'aria-label' => $this->title === null ? ($this->ariaLabel ?? 'Dialog') : null,
                'data-serixa-modal-dialog' => true,
                'tabindex' => '-1',
            ],
        );

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('modal', ['open' => $this->open]),
            [
                'id' => $id,
                'data-serixa-modal' => true,
                'data-serixa-open' => $this->open ? 'true' : 'false',
                'data-serixa-dismissible' => $this->dismissible ? 'true' : 'false',
                'hidden' => $this->open ? null : true,
            ],
        );

        return sprintf(
            '<div%s><div%s data-serixa-modal-backdrop></div><div%s>%s%s%s%s</div></div>',
            $rootAttrs,
            $this->buildStructuralAttributeString($this->theme()->classes('modal.backdrop')),
            $dialogAttrs,
            $close,
            $titleHtml,
            $bodyHtml,
            $footerHtml,
        );
    }
}
