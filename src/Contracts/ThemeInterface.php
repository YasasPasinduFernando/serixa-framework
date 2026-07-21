<?php

declare(strict_types=1);

namespace Serixa\Contracts;

use Serixa\Token\DesignToken;

/**
 * Maps widget intent + design tokens to CSS class lists.
 *
 * Themes (Default, Bootstrap, Material, custom) implement this contract.
 * Widgets call classes() / token(); they never hard-code utility frameworks.
 */
interface ThemeInterface
{
    /**
     * Resolve CSS classes for a named widget and its render state.
     *
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    public function classes(string $component, array $state = []): array;

    /**
     * Resolve CSS classes for a design token.
     *
     * @return list<string>
     */
    public function token(DesignToken|string $token): array;

    /**
     * @return list<string>
     */
    public function buttonPrimary(): array;

    /**
     * @return list<string>
     */
    public function buttonSecondary(): array;

    /**
     * @return list<string>
     */
    public function buttonDanger(): array;

    /**
     * @return list<string>
     */
    public function buttonGhost(): array;
}
