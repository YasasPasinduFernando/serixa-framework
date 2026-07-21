<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\HasChildren;
use Serixa\Component\Concerns\HasDomEvents;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;

/**
 * Semantic HTML form. Render-only — no submission or validation engine.
 *
 * @example Form::make()->method('POST')->action('/login')->onSubmit('login')
 */
final class Form extends Component
{
    use AssertsOption;
    use HasChildren;
    use HasDomEvents;
    use ResolvesTheme;

    private string $method = 'POST';

    private ?string $action = null;

    private ?string $enctype = null;

    private ?string $target = null;

    private bool $novalidate = false;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param WidgetInterface|string|list<WidgetInterface|string> ...$children
     */
    public static function make(WidgetInterface|string|array ...$children): self
    {
        $form = new self();
        $form->setChildren(self::normalizeChildren($children));

        return $form;
    }

    public function method(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    public function action(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function enctype(string $enctype): self
    {
        $this->assertOption($enctype, [
            'application/x-www-form-urlencoded',
            'multipart/form-data',
            'text/plain',
        ], 'enctype');
        $this->enctype = $enctype;

        return $this;
    }

    public function target(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function novalidate(bool $novalidate = true): self
    {
        $this->novalidate = $novalidate;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('form'),
            array_merge($this->domEventAttributes(), [
                'method' => $this->method,
                'action' => $this->action,
                'enctype' => $this->enctype,
                'target' => $this->target,
                'novalidate' => $this->novalidate ? true : null,
            ]),
        );

        return sprintf('<form%s>%s</form>', $attrs, $this->renderChildren($this->getChildren()));
    }
}
