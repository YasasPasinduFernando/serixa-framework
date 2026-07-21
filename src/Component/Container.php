<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\ComponentInterface;

/**
 * Layout wrapper with responsive max-width and padding. Owns structure, not content.
 *
 * @example Container::make()->child(Text::make('Hello'))->maxWidth('lg')
 */
final class Container extends Component
{
    use AssertsOption;
    use HasChildren;
    use ResolvesTheme;

    private string $maxWidth = 'lg';

    private string $padding = 'md';

    private function __construct(ComponentInterface|string ...$children)
    {
        parent::__construct();
        $this->setChildren(array_values($children));
    }

    public static function make(ComponentInterface|string ...$children): self
    {
        return new self(...$children);
    }

    public function maxWidth(string $maxWidth): self
    {
        $this->assertOption($maxWidth, ['sm', 'md', 'lg', 'xl', 'full'], 'maxWidth');
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function padding(string $padding): self
    {
        $this->assertOption($padding, ['none', 'sm', 'md', 'lg'], 'padding');
        $this->padding = $padding;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('container', [
                'maxWidth' => $this->maxWidth,
                'padding' => $this->padding,
            ]),
        );

        return sprintf(
            '<div%s>%s</div>',
            $attrs,
            $this->renderChildren($this->getChildren()),
        );
    }
}
