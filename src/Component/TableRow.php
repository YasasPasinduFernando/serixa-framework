<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/** Table &lt;tr&gt;. */
final class TableRow extends Component
{
    use HasChildren;
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
        $row = new self();
        $row->setChildren(self::normalizeChildren($children));

        return $row;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('tableRow'));

        return sprintf('<tr%s>%s</tr>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
