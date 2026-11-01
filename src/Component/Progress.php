<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\AssertsOption;
use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Exception\InvalidArgumentException;

/**
 * Progress bar (determinate or indeterminate).
 *
 * @example Progress::make()->value(40)->max(100)->label('Upload')
 */
final class Progress extends Component
{
    use AssertsOption;
    use ResolvesTheme;

    private ?float $value = null;

    private float $max = 100.0;

    private ?string $label = null;

    private string $size = 'md';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function value(float|int $value): self
    {
        $value = (float) $value;

        if (! is_finite($value)) {
            throw new InvalidArgumentException('Progress value must be a finite number.');
        }

        $this->value = $value;

        return $this;
    }

    public function max(float|int $max): self
    {
        $max = (float) $max;

        if (! is_finite($max) || $max <= 0) {
            throw new InvalidArgumentException('Progress max must be a finite number greater than zero.');
        }

        $this->max = $max;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function size(string $size): self
    {
        $this->assertOption($size, ['sm', 'md', 'lg'], 'size');
        $this->size = $size;

        return $this;
    }

    public function render(): string
    {
        $indeterminate = $this->value === null;
        $normalizedValue = $indeterminate ? null : max(0.0, min($this->max, $this->value ?? 0.0));
        $percent = $indeterminate ? null : (($normalizedValue ?? 0.0) / $this->max) * 100.0;

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('progress', ['size' => $this->size, 'indeterminate' => $indeterminate]),
            [
                'role' => 'progressbar',
                'aria-valuemin' => '0',
                'aria-valuemax' => (string) $this->max,
                'aria-valuenow' => $indeterminate ? null : (string) $normalizedValue,
                'aria-label' => $this->label ?? 'Progress',
                'data-serixa-progress' => true,
            ],
        );

        $barStyle = $indeterminate ? null : sprintf('width: %s%%', rtrim(rtrim(number_format($percent ?? 0, 2, '.', ''), '0'), '.'));

        return sprintf(
            '<div%s><div%s%s></div></div>',
            $attrs,
            $this->buildStructuralAttributeString($this->theme()->classes('progress.bar', [
                'indeterminate' => $indeterminate,
            ])),
            $barStyle !== null ? sprintf(' style="%s"', $this->escape($barStyle)) : '',
        );
    }
}
