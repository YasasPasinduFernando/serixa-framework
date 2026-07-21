<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Component\Concerns\ResolvesWidgetId;
use Serixa\Contracts\WidgetInterface;

/**
 * Side drawer overlay (SSR + DrawerModule).
 *
 * @example Drawer::make()->side('right')->title('Filters')->body(...)
 */
final class Drawer extends Component
{
    use AssertsOption;
    use ResolvesTheme;
    use ResolvesWidgetId;

    private string $side = 'right';

    private ?string $title = null;

    private WidgetInterface|string|null $body = null;

    private bool $open = false;

    private bool $dismissible = true;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function side(string $side): self
    {
        $this->assertOption($side, ['left', 'right'], 'side');
        $this->side = $side;

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

    public function childrenForTree(): array
    {
        return $this->body !== null ? [$this->body] : [];
    }

    public function render(): string
    {
        $id = $this->resolveWidgetId('drawer');
        $titleId = $id . '-title';

        $titleHtml = $this->title !== null
            ? sprintf(
                '<h2%s id="%s">%s</h2>',
                $this->buildStructuralAttributeString($this->theme()->classes('drawer.title')),
                $this->escape($titleId),
                $this->escape($this->title),
            )
            : '';

        $bodyHtml = $this->body !== null
            ? sprintf(
                '<div%s>%s</div>',
                $this->buildStructuralAttributeString($this->theme()->classes('drawer.body')),
                $this->renderChild($this->body),
            )
            : '';

        $close = $this->dismissible
            ? sprintf(
                '<button type="button"%s data-serixa-close aria-label="Close">×</button>',
                $this->buildStructuralAttributeString($this->theme()->classes('drawer.close')),
            )
            : '';

        $panelAttrs = $this->buildStructuralAttributeString(
            $this->theme()->classes('drawer.panel', ['side' => $this->side]),
            [
                'role' => 'dialog',
                'aria-modal' => 'true',
                'aria-labelledby' => $this->title !== null ? $titleId : null,
                'data-serixa-drawer-panel' => true,
                'tabindex' => '-1',
            ],
        );

        $rootAttrs = $this->buildAttributeString(
            $this->theme()->classes('drawer', ['open' => $this->open, 'side' => $this->side]),
            [
                'id' => $id,
                'data-serixa-drawer' => true,
                'data-serixa-open' => $this->open ? 'true' : 'false',
                'data-serixa-dismissible' => $this->dismissible ? 'true' : 'false',
                'hidden' => $this->open ? null : true,
            ],
        );

        return sprintf(
            '<div%s><div%s data-serixa-drawer-backdrop></div><div%s>%s%s%s</div></div>',
            $rootAttrs,
            $this->buildStructuralAttributeString($this->theme()->classes('drawer.backdrop')),
            $panelAttrs,
            $close,
            $titleHtml,
            $bodyHtml,
        );
    }
}
