<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/** Table &lt;th&gt;. */
final class TableHeaderCell extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private string $scope = 'col';

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $cell = new self();
        $cell->setChildren(self::normalizeChildren($children));

        return $cell;
    }

    public function scope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('tableHeaderCell'),
            ['scope' => $this->scope],
        );

        return sprintf('<th%s>%s</th>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
