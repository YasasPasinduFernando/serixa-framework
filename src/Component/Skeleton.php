<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;

/**
 * Content placeholder skeleton.
 *
 * @example Skeleton::make()->lines(3)
 */
final class Skeleton extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private string $variant = 'text';

    private int $lines = 1;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function variant(string $variant): self
    {
        $this->assertOption($variant, ['text', 'avatar', 'card', 'rect'], 'variant');
        $this->variant = $variant;

        return $this;
    }

    public function lines(int $lines): self
    {
        $this->lines = max(1, $lines);

        return $this;
    }

    public function render(): string
    {
        if ($this->variant !== 'text' || $this->lines === 1) {
            return sprintf(
                '<div%s aria-hidden="true" data-serixa-skeleton></div>',
                $this->buildAttributeString($this->theme()->classes('skeleton', ['variant' => $this->variant])),
            );
        }

        $parts = [];

        for ($i = 0; $i < $this->lines; $i++) {
            $parts[] = sprintf(
                '<div%s aria-hidden="true"></div>',
                $this->buildStructuralAttributeString($this->theme()->classes('skeleton', ['variant' => 'text'])),
            );
        }

        return sprintf(
            '<div%s role="status" aria-label="Loading" data-serixa-skeleton>%s</div>',
            $this->buildAttributeString($this->theme()->classes('skeleton.group')),
            implode('', $parts),
        );
    }
}
