<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Groups label, control, helper, and error text with consistent spacing.
 *
 * @example FieldGroup::make(Label::make('Email')->for('email'), Input::make()->id('email'))
 */
final class FieldGroup extends Component
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
        $group = new self();
        $group->setChildren(self::normalizeChildren($children));

        return $group;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('fieldGroup'));

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
