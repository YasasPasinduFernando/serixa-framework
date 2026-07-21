<?php

declare(strict_types=1);

namespace Serixa\Widget;

use Serixa\Accessibility\AccessibilityBuilder;
use Serixa\Contracts\WidgetInterface;
use Serixa\State\StateManager;
use Serixa\State\WidgetState;
use Serixa\Support\AttributeBag;
use Serixa\Support\ClassMerger;
use Serixa\Support\HtmlEscaper;

/**
 * Abstract widget: attributes, chaining, lifecycle defaults, optional state, a11y helpers.
 *
 * Concrete UI widgets live under Serixa\Component for backward compatibility
 * and extend either this class or the Component alias.
 */
abstract class Widget implements WidgetInterface
{
    protected ?string $id = null;

    /** @var list<string> */
    protected array $classes = [];

    /** @var array<string, string|bool|null> */
    protected array $attributes = [];

    protected ?string $stateKey = null;

    private bool $mounted = false;

    private ?AccessibilityBuilder $accessibility = null;

    private static ?HtmlEscaper $sharedEscaper = null;

    private static ?AttributeBag $sharedAttributeBag = null;

    private static ?ClassMerger $sharedClassMerger = null;

    protected readonly HtmlEscaper $escaper;

    protected readonly AttributeBag $attributeBag;

    protected readonly ClassMerger $classMerger;

    public function __construct(
        ?HtmlEscaper $escaper = null,
        ?AttributeBag $attributeBag = null,
        ?ClassMerger $classMerger = null,
    ) {
        $this->escaper = $escaper ?? (self::$sharedEscaper ??= new HtmlEscaper());
        $this->attributeBag = $attributeBag ?? (self::$sharedAttributeBag ??= new AttributeBag());
        $this->classMerger = $classMerger ?? (self::$sharedClassMerger ??= new ClassMerger());
    }

    /**
     * @return static
     */
    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return static
     */
    public function class(string ...$classes): static
    {
        foreach ($classes as $class) {
            foreach (preg_split('/\s+/', trim($class)) ?: [] as $token) {
                if ($token !== '') {
                    $this->classes[] = $token;
                }
            }
        }

        return $this;
    }

    /**
     * @return static
     */
    public function attribute(string $name, string|bool|null $value): static
    {
        if ($value === null) {
            unset($this->attributes[$name]);
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    /**
     * @param array<string, string|bool|null> $attributes
     * @return static
     */
    public function attributes(array $attributes): static
    {
        foreach ($attributes as $name => $value) {
            $this->attribute($name, $value);
        }

        return $this;
    }

    /**
     * Bind this widget to a StateManager key (memory-only).
     *
     * @return static
     */
    public function stateKey(string $key): static
    {
        $this->stateKey = $key;

        return $this;
    }

    public function getStateKey(): ?string
    {
        return $this->stateKey;
    }

    /**
     * Optional per-widget state bag from the process StateManager.
     */
    protected function state(?StateManager $manager = null): WidgetState
    {
        $manager ??= StateManager::current();
        $key = $this->stateKey ?? ('widget_' . spl_object_id($this));

        return $manager->get($key);
    }

    public function accessibility(): AccessibilityBuilder
    {
        return $this->accessibility ??= new AccessibilityBuilder();
    }

    public function beforeMount(): void
    {
    }

    public function mounted(): void
    {
        $this->mounted = true;
    }

    public function beforeRender(): void
    {
    }

    public function afterRender(): void
    {
    }

    public function destroy(): void
    {
        $this->mounted = false;
    }

    public function isMounted(): bool
    {
        return $this->mounted;
    }

    /**
     * @return list<WidgetInterface|string>
     */
    public function childrenForTree(): array
    {
        return [];
    }

    abstract public function render(): string;

    /**
     * @param list<string> $frameworkClasses
     * @param array<string, string|bool|null> $extraAttributes
     * @param bool $inheritWidgetState When false, omit widget id/class/attributes/a11y
     *                                 (use for nested wrappers/labels to prevent leakage).
     */
    protected function buildAttributeString(
        array $frameworkClasses = [],
        array $extraAttributes = [],
        bool $inheritWidgetState = true,
    ): string {
        if (! $inheritWidgetState) {
            $classString = $this->classMerger->toString($frameworkClasses, []);
            $attributes = $extraAttributes;
        } else {
            $classString = $this->classMerger->toString($frameworkClasses, $this->classes);
            $attributes = array_merge($this->attributes, $extraAttributes);

            if ($this->accessibility !== null) {
                $attributes = array_merge($attributes, $this->accessibility->toAttributes());
            }

            if ($this->id !== null) {
                $attributes['id'] = $this->id;
            }
        }

        if ($classString !== '') {
            $attributes['class'] = $classString;
        }

        return $this->attributeBag->build($attributes);
    }

    /**
     * Attributes for nested structural nodes (wrapper, label chrome, icons).
     * Never inherits interactive widget state.
     *
     * @param list<string> $frameworkClasses
     * @param array<string, string|bool|null> $extraAttributes
     */
    protected function buildStructuralAttributeString(
        array $frameworkClasses = [],
        array $extraAttributes = [],
    ): string {
        return $this->buildAttributeString($frameworkClasses, $extraAttributes, false);
    }

    protected function escape(string $value): string
    {
        return $this->escaper->escape($value);
    }

    protected function renderChild(WidgetInterface|string $child): string
    {
        if ($child instanceof WidgetInterface) {
            return $child->render();
        }

        return $this->escape($child);
    }

    /**
     * @param list<WidgetInterface|string> $children
     */
    protected function renderChildren(array $children): string
    {
        $html = '';

        foreach ($children as $child) {
            $html .= $this->renderChild($child);
        }

        return $html;
    }
}
