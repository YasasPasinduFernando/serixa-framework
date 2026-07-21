<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/** Table &lt;thead&gt;. */
final class TableHead extends Component
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
        $head = new self();
        $head->setChildren(self::normalizeChildren($children));

        return $head;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('tableHead'));

        return sprintf('<thead%s>%s</thead>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
