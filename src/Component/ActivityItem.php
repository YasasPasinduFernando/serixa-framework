<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Single activity feed entry.
 *
 * @example ActivityItem::make()->title('Invoice paid')->timestamp('2h ago')
 */
final class ActivityItem extends Component
{
    use ResolvesTheme;

    private WidgetInterface|string|null $icon = null;

    private ?string $title = null;

    private ?string $description = null;

    private ?string $timestamp = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function icon(WidgetInterface|string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function timestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;

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
                $this->buildAttributeString($this->theme()->classes('activityItem.icon')),
                $this->renderChild($this->icon),
            );
        }

        $body = [];

        if ($this->title !== null) {
            $body[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('activityItem.title')),
                $this->escape($this->title),
            );
        }

        if ($this->description !== null) {
            $body[] = sprintf(
                '<div%s>%s</div>',
                $this->buildAttributeString($this->theme()->classes('activityItem.description')),
                $this->escape($this->description),
            );
        }

        if ($this->timestamp !== null) {
            $body[] = sprintf(
                '<time%s>%s</time>',
                $this->buildAttributeString($this->theme()->classes('activityItem.timestamp')),
                $this->escape($this->timestamp),
            );
        }

        $parts[] = sprintf(
            '<div%s>%s</div>',
            $this->buildAttributeString($this->theme()->classes('activityItem.body')),
            implode('', $body),
        );

        return sprintf(
            '<li%s>%s</li>',
            $this->buildAttributeString($this->theme()->classes('activityItem'), ['role' => 'listitem']),
            implode('', $parts),
        );
    }
}
