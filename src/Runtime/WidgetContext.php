<?php

declare(strict_types=1);

namespace Serixa\Runtime;

use Serixa\Contracts\ThemeInterface;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Theme\DefaultTheme;

/**
 * Immutable runtime facts available during a render (theme, locale, mode, etc.).
 *
 * Prefer passing this through RenderContext over process-wide static lookups.
 */
final class WidgetContext
{
    /**
     * @param array<string, bool> $featureFlags
     */
    public function __construct(
        private readonly ThemeInterface $theme = new DefaultTheme(),
        private readonly string $locale = 'en',
        private readonly string $textDirection = 'ltr',
        private readonly string $colorMode = 'light',
        private readonly string $breakpoint = 'md',
        private readonly string $environment = 'production',
        private readonly array $featureFlags = [],
    ) {
        if (! in_array($this->textDirection, ['ltr', 'rtl'], true)) {
            throw new InvalidArgumentException('textDirection must be ltr or rtl.');
        }

        if (! in_array($this->colorMode, ['light', 'dark', 'system'], true)) {
            throw new InvalidArgumentException('colorMode must be light, dark, or system.');
        }
    }

    public static function defaults(): self
    {
        return new self();
    }

    public function theme(): ThemeInterface
    {
        return $this->theme;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function textDirection(): string
    {
        return $this->textDirection;
    }

    public function colorMode(): string
    {
        return $this->colorMode;
    }

    public function breakpoint(): string
    {
        return $this->breakpoint;
    }

    public function environment(): string
    {
        return $this->environment;
    }

    /**
     * @return array<string, bool>
     */
    public function featureFlags(): array
    {
        return $this->featureFlags;
    }

    public function hasFeature(string $flag): bool
    {
        return ($this->featureFlags[$flag] ?? false) === true;
    }

    public function withTheme(ThemeInterface $theme): self
    {
        return new self(
            $theme,
            $this->locale,
            $this->textDirection,
            $this->colorMode,
            $this->breakpoint,
            $this->environment,
            $this->featureFlags,
        );
    }

    public function withLocale(string $locale): self
    {
        return new self(
            $this->theme,
            $locale,
            $this->textDirection,
            $this->colorMode,
            $this->breakpoint,
            $this->environment,
            $this->featureFlags,
        );
    }

    public function withTextDirection(string $textDirection): self
    {
        return new self(
            $this->theme,
            $this->locale,
            $textDirection,
            $this->colorMode,
            $this->breakpoint,
            $this->environment,
            $this->featureFlags,
        );
    }

    public function withColorMode(string $colorMode): self
    {
        return new self(
            $this->theme,
            $this->locale,
            $this->textDirection,
            $colorMode,
            $this->breakpoint,
            $this->environment,
            $this->featureFlags,
        );
    }

    public function withBreakpoint(string $breakpoint): self
    {
        return new self(
            $this->theme,
            $this->locale,
            $this->textDirection,
            $this->colorMode,
            $breakpoint,
            $this->environment,
            $this->featureFlags,
        );
    }

    public function withEnvironment(string $environment): self
    {
        return new self(
            $this->theme,
            $this->locale,
            $this->textDirection,
            $this->colorMode,
            $this->breakpoint,
            $environment,
            $this->featureFlags,
        );
    }

    /**
     * @param array<string, bool> $featureFlags
     */
    public function withFeatureFlags(array $featureFlags): self
    {
        return new self(
            $this->theme,
            $this->locale,
            $this->textDirection,
            $this->colorMode,
            $this->breakpoint,
            $this->environment,
            $featureFlags,
        );
    }
}
