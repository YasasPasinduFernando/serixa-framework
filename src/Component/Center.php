<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\HasFill;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Centers children on both axes via flex.
 *
 * Does not force width; as a block-level flex box it already stretches in normal flow.
 * Call fill() when used as a flex child that must span the parent.
 */
final class Center extends Component
{
    use HasChildren;
    use HasFill;
    use ResolvesTheme;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $center = new self();
        $center->setChildren(self::normalizeChildren($children));

        return $center;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('center', [
            'fill' => $this->fillState(),
        ]));

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
