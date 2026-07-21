<?php

declare(strict_types=1);

namespace Serixa\Component\Concerns;

/**
 * Shared form-control attributes (name, id, disabled, required, value, HTML constraints).
 *
 * Emits native HTML / ARIA only — no validation engine.
 *
 * @internal
 */
trait HasFormControl
{
    private ?string $name = null;

    private ?string $value = null;

    private bool $disabled = false;

    private bool $required = false;

    private bool $invalid = false;

    private bool $readonly = false;

    private string|int|float|null $min = null;

    private string|int|float|null $max = null;

    private ?int $minLength = null;

    private ?int $maxLength = null;

    private ?string $pattern = null;

    private ?string $describedBy = null;

    /**
     * @return static
     */
    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return static
     */
    public function value(string|int|float|null $value): static
    {
        $this->value = $value === null ? null : (string) $value;

        return $this;
    }

    /**
     * @return static
     */
    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * @return static
     */
    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    /**
     * HTML readonly attribute (not the same as disabled).
     *
     * @return static
     */
    public function readonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;

        return $this;
    }

    /**
     * HTML min constraint (number/date inputs).
     *
     * @return static
     */
    public function min(string|int|float $min): static
    {
        $this->min = $min;

        return $this;
    }

    /**
     * HTML max constraint (number/date inputs).
     *
     * @return static
     */
    public function max(string|int|float $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * HTML minlength constraint.
     *
     * @return static
     */
    public function minLength(int $minLength): static
    {
        $this->minLength = max(0, $minLength);

        return $this;
    }

    /**
     * HTML maxlength constraint.
     *
     * @return static
     */
    public function maxLength(int $maxLength): static
    {
        $this->maxLength = max(0, $maxLength);

        return $this;
    }

    /**
     * HTML pattern constraint (regular expression).
     *
     * @return static
     */
    public function pattern(string $pattern): static
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * UI-only invalid flag (no validation engine). Sets aria-invalid.
     *
     * @return static
     */
    public function invalid(bool $invalid = true): static
    {
        $this->invalid = $invalid;

        return $this;
    }

    /**
     * Space-separated element ids for aria-describedby (helper / error text).
     *
     * @return static
     */
    public function describedBy(string $ids): static
    {
        $this->describedBy = $ids;

        return $this;
    }

    /**
     * @return array<string, string|bool|null>
     */
    protected function formControlAttributes(): array
    {
        return [
            'name' => $this->name,
            'disabled' => $this->disabled ? true : null,
            'required' => $this->required ? true : null,
            'readonly' => $this->readonly ? true : null,
            'min' => $this->min !== null ? (string) $this->min : null,
            'max' => $this->max !== null ? (string) $this->max : null,
            'minlength' => $this->minLength !== null ? (string) $this->minLength : null,
            'maxlength' => $this->maxLength !== null ? (string) $this->maxLength : null,
            'pattern' => $this->pattern,
            'aria-invalid' => $this->invalid ? 'true' : null,
            'aria-disabled' => $this->disabled ? 'true' : null,
            'aria-required' => $this->required ? 'true' : null,
            'aria-readonly' => $this->readonly ? 'true' : null,
            'aria-describedby' => $this->describedBy,
        ];
    }
}
