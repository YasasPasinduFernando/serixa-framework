<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Loading spinner.
 *
 * @example Spinner::make()->size('md')->label('Loading')
 */
final class Spinner extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $size = 'md';

    private string $label = 'Loading';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function size(string $size): self
    {
        $this->assertOption($size, ['sm', 'md', 'lg'], 'size');
        $this->size = $size;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString(
            $this->theme()->classes('spinner', ['size' => $this->size]),
            [
                'role' => 'status',
                'aria-live' => 'polite',
                'aria-label' => $this->label,
                'data-serixa-spinner' => true,
            ],
        );

        return sprintf(
            '<span%s><span%s aria-hidden="true"></span></span>',
            $attrs,
            $this->buildStructuralAttributeString($this->theme()->classes('spinner.glyph', ['size' => $this->size])),
        );
    }
}
