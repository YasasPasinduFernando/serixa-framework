<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * Spacing wrapper. Prefer the fluent builder; theme maps sides to Tailwind classes.
 *
 * @example Padding::make()->all(4)->child(Text::make('Inset'))
 * @example Padding::make()->x(6)->y(4)->child(Text::make('Inset'))
 *
 * Side setters are implemented with {@see __call} / {@see __callStatic} so fluent
 * instance methods and deprecated static factories can share the same names
 * (PHP cannot declare both).
 *
 * @method self all(int|float|string $value)
 * @method self x(int|float|string $value)
 * @method self y(int|float|string $value)
 * @method self top(int|float|string $value)
 * @method self bottom(int|float|string $value)
 * @method self left(int|float|string $value)
 * @method self right(int|float|string $value)
 */
final class Padding extends Component
{
    use HasChildren;
    use ResolvesTheme;

    private const SIDES = ['all', 'x', 'y', 'top', 'bottom', 'left', 'right'];

    /** @var array<string, int|float|string|null> */
    private array $sides = [];

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $padding = new self();
        $padding->setChildren(self::normalizeChildren($children));

        return $padding;
    }

    /** Tailwind scale shortcut: Padding::make()->sm()->child(...) */
    public function sm(): self
    {
        $this->sides['all'] = 4;

        return $this;
    }

    /** Tailwind scale shortcut: Padding::make()->md()->child(...) */
    public function md(): self
    {
        $this->sides['all'] = 6;

        return $this;
    }

    /** Tailwind scale shortcut: Padding::make()->lg()->child(...) */
    public function lg(): self
    {
        $this->sides['all'] = 8;

        return $this;
    }

    /**
     * Fluent side setters.
     *
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): self
    {
        if (! in_array($name, self::SIDES, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown Padding method "%s". Allowed: %s, plus sm()/md()/lg().',
                $name,
                implode(', ', self::SIDES),
            ));
        }

        if ($arguments === []) {
            throw new InvalidArgumentException(sprintf('Padding::%s() requires a value.', $name));
        }

        /** @var int|float|string $value */
        $value = $arguments[0];
        $this->sides[$name] = $value;

        return $this;
    }

    /**
     * Deprecated static factories: Padding::all(4), Padding::x(2, $child), …
     *
     * @param list<mixed> $arguments
     *
     * @deprecated Use Padding::make()->all($value)->child(...) (or x/y/top/…)
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        if (! in_array($name, self::SIDES, true)) {
            throw new InvalidArgumentException(sprintf('Unknown Padding static method: %s', $name));
        }

        if ($arguments === []) {
            throw new InvalidArgumentException(sprintf('Padding::%s() requires a value.', $name));
        }

        /** @var int|float|string $value */
        $value = $arguments[0];
        /** @var array<int, mixed> $children */
        $children = array_slice($arguments, 1);

        return self::factory($name, $value, $children);
    }

    /**
     * @param array<int, mixed> $children
     */
    private static function factory(string $side, int|float|string $value, array $children): self
    {
        $padding = new self();
        $padding->sides[$side] = $value;
        $padding->setChildren(self::normalizeChildren($children));

        return $padding;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('padding', $this->sides),
        );

        return sprintf('<div%s>%s</div>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
