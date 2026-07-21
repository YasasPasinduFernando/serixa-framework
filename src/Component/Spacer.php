<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Flexible empty space inside a Row or Column (Flutter Spacer).
 */
final class Spacer extends Component
{
    use ResolvesTheme;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function render(): string
    {
        $attrs = $this->buildAttributeString($this->theme()->classes('spacer'));

        return sprintf('<div%s></div>', $attrs);
    }
}
