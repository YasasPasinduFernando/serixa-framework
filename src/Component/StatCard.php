<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * KPI / statistic card for dashboards.
 *
 * @example StatCard::make()->title('Users')->value('1,284')->trend('+4.2%')
 */
final class StatCard extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private ?string $title = null;

    private ?string $value = null;

    private ?string $subtitle = null;

    private WidgetInterface|string|null $icon = null;

    private ?string $trend = null;

    private string $color = 'default';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function subtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function icon(WidgetInterface|string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function trend(string $trend): self
    {
        $this->trend = $trend;

        return $this;
    }

    public function color(string $color): self
    {
        $this->assertOption($color, ['default', 'primary', 'success', 'warning', 'danger'], 'color');
        $this->color = $color;

        return $this;
    }

    public function childrenForTree(): array
    {
        return $this->icon !== null ? [$this->icon] : [];
    }

    public function render(): string
    {
        $parts = [];

        if ($this->icon !== null) {
            $parts[] = sprintf(
                '<div%s aria-hidden="true">%s</div>',
                $this->buildAttributeString($this->theme()->classes('statCard.icon')),
                $this->renderChild($this->icon),
            );
        }

        if ($this->title !== null) {
            $parts[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('statCard.title')),
                $this->escape($this->title),
            );
        }

        if ($this->value !== null) {
            $parts[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('statCard.value')),
                $this->escape($this->value),
            );
        }

        if ($this->subtitle !== null) {
            $parts[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('statCard.subtitle')),
                $this->escape($this->subtitle),
            );
        }

        if ($this->trend !== null) {
            $parts[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('statCard.trend')),
                $this->escape($this->trend),
            );
        }

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('statCard', ['color' => $this->color]),
        );

        return sprintf('<div%s>%s</div>', $attrs, implode('', $parts));
    }
}
