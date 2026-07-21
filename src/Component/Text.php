<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Exception\InvalidArgumentException;

/**
 * Semantic text node. Intent maps through the active theme.
 *
 * @example Text::make('Hello')->as('h1')->size('xl')->weight('bold')
 */
final class Text extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $content;

    private string $tag = 'p';

    private string $size = 'md';

    private string $weight = 'normal';

    private string $tone = 'default';

    private function __construct(string $content)
    {
        parent::__construct();
        $this->content = $content;
    }

    public static function make(string $content): self
    {
        return new self($content);
    }

    /**
     * HTML tag used for this text node (e.g. p, span, h1â€“h6, label).
     */
    public function as(string $tag): self
    {
        $tag = strtolower(trim($tag));

        if ($tag === '' || ! preg_match('/^[a-z][a-z0-9]*$/', $tag)) {
            throw new InvalidArgumentException(sprintf('Invalid HTML tag: %s', $tag));
        }

        $this->tag = $tag;

        return $this;
    }

    public function size(string $size): self
    {
        $this->assertOption($size, ['xs', 'sm', 'md', 'lg', 'xl', '2xl'], 'size');
        $this->size = $size;

        return $this;
    }

    public function weight(string $weight): self
    {
        $this->assertOption($weight, ['normal', 'medium', 'semibold', 'bold'], 'weight');
        $this->weight = $weight;

        return $this;
    }

    public function tone(string $tone): self
    {
        $this->assertOption($tone, ['default', 'muted', 'primary', 'danger'], 'tone');
        $this->tone = $tone;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('text', [
                'size' => $this->size,
                'weight' => $this->weight,
                'tone' => $this->tone,
            ]),
        );

        return sprintf(
            '<%1$s%2$s>%3$s</%1$s>',
            $this->tag,
            $attrs,
            $this->escape($this->content),
        );
    }
}
