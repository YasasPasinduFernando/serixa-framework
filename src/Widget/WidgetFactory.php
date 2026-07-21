<?php

declare(strict_types=1);

namespace Serixa\Widget;

use Serixa\Component\Button;
use Serixa\Component\Card;
use Serixa\Component\Center;
use Serixa\Component\Checkbox;
use Serixa\Component\Column;
use Serixa\Component\Container;
use Serixa\Component\Divider;
use Serixa\Component\Expanded;
use Serixa\Component\Form;
use Serixa\Component\Input;
use Serixa\Component\Link;
use Serixa\Component\Padding;
use Serixa\Component\Row;
use Serixa\Component\Scaffold;
use Serixa\Component\Select;
use Serixa\Component\SizedBox;
use Serixa\Component\Spacer;
use Serixa\Component\Stack;
use Serixa\Component\Text;
use Serixa\Component\Textarea;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Named widget construction for registries, serializers, and future DevTools.
 *
 * Does not replace fluent make() APIs — those remain the primary DX.
 */
final class WidgetFactory
{
    /** @var array<string, class-string<WidgetInterface>> */
    private array $map;

    /**
     * @param array<string, class-string<WidgetInterface>> $map
     */
    public function __construct(array $map = [])
    {
        $this->map = $map === [] ? self::defaults() : $map;
    }

    /**
     * @return array<string, class-string<WidgetInterface>>
     */
    public static function defaults(): array
    {
        return [
            'button' => Button::class,
            'card' => Card::class,
            'text' => Text::class,
            'container' => Container::class,
            'row' => Row::class,
            'column' => Column::class,
            'center' => Center::class,
            'padding' => Padding::class,
            'spacer' => Spacer::class,
            'expanded' => Expanded::class,
            'sizedBox' => SizedBox::class,
            'stack' => Stack::class,
            'divider' => Divider::class,
            'scaffold' => Scaffold::class,
            'form' => Form::class,
            'input' => Input::class,
            'textarea' => Textarea::class,
            'checkbox' => Checkbox::class,
            'select' => Select::class,
            'link' => Link::class,
        ];
    }

    /**
     * @param class-string<WidgetInterface> $class
     */
    public function register(string $name, string $class): self
    {
        $this->map[$name] = $class;

        return $this;
    }

    /**
     * Create a widget by registry name. Pass constructor args for make()-style classes.
     *
     * @param mixed ...$args
     */
    public function make(string $name, mixed ...$args): WidgetInterface
    {
        if (! isset($this->map[$name])) {
            throw new InvalidArgumentException(sprintf('Unknown widget "%s".', $name));
        }

        $class = $this->map[$name];

        if (! method_exists($class, 'make')) {
            throw new InvalidArgumentException(sprintf('Widget "%s" has no make() factory.', $name));
        }

        /** @var WidgetInterface $widget */
        $widget = $class::make(...$args);

        return $widget;
    }

    public function has(string $name): bool
    {
        return isset($this->map[$name]);
    }
}
