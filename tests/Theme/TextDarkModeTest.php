<?php

declare(strict_types=1);

namespace Serixa\Tests\Theme;

use PHPUnit\Framework\TestCase;
use Serixa\Theme\DefaultTheme;

final class TextDarkModeTest extends TestCase
{
    public function testTextRetainsDarkModeTokenClasses(): void
    {
        $theme = new DefaultTheme();
        $this->assertContains('dark:text-slate-100', $theme->classes('text', ['tone' => 'default']));
        $this->assertContains('dark:text-slate-400', $theme->classes('text', ['tone' => 'muted']));
    }
}
