<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Vertical activity feed.
 *
 * @example ActivityList::make(ActivityItem::make()->title('Joined'))
 */
final class ActivityList extends Component
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
        $list = new self();
        $list->setChildren(self::normalizeChildren($children));

        return $list;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('activityList'),
            ['role' => 'list'],
        );

        return sprintf('<ul%s>%s</ul>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
