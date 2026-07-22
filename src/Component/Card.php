<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Content card with optional title. Renders as a semantic article.
 *
 * @example Card::make()->title('Profile')->child(Text::make('Details'))
 */
final class Card extends Component
{
    use AssertsOption;
    use HasChildren;
    use ResolvesTheme;

    private ?string $title = null;

    private string $padding = 'md';

    private function __construct(WidgetInterface|string ...$children)
    {
        parent::__construct();
        $this->setChildren(array_values($children));
    }

    public static function make(WidgetInterface|string ...$children): self
    {
        return new self(...$children);
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function padding(string $padding): self
    {
        $this->assertOption($padding, ['sm', 'md', 'lg'], 'padding');
        $this->padding = $padding;

        return $this;
    }

    public function render(): string
    {
        $titleId = null;
        $header = '';

        if ($this->title !== null) {
            $titleId = ($this->id ?? ('card-' . spl_object_id($this))) . '-title';
            $titleClasses = implode(' ', $this->theme()->classes('card.title'));
            $header = sprintf(
                '<header><h2 id="%s" class="%s">%s</h2></header>',
                $this->escape($titleId),
                $this->escape($titleClasses),
                $this->escape($this->title),
            );
        }

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('card', ['padding' => $this->padding]),
            $titleId !== null ? ['aria-labelledby' => $titleId] : [],
        );

        return sprintf(
            '<article%s>%s%s</article>',
            $attrs,
            $header,
            $this->renderChildren($this->getChildren()),
        );
    }
}
