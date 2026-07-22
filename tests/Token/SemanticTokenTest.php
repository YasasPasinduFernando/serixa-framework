<?php

declare(strict_types=1);

namespace Serixa\Tests\Token;

use PHPUnit\Framework\TestCase;
use Serixa\Theme\DefaultTheme;
use Serixa\Theme\ThemeManager;
use Serixa\Token\BorderToken;
use Serixa\Token\BreakpointToken;
use Serixa\Token\OpacityToken;
use Serixa\Token\TransitionToken;
use Serixa\Token\ZIndexToken;

final class SemanticTokenTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testNewSemanticTokensResolve(): void
    {
        $theme = new DefaultTheme();
        $this->assertNotEmpty($theme->token(BorderToken::focus()));
        $this->assertNotEmpty($theme->token(OpacityToken::disabled()));
        $this->assertNotEmpty($theme->token(ZIndexToken::modal()));
        $this->assertNotEmpty($theme->token(BreakpointToken::md()));
        $this->assertNotEmpty($theme->token(TransitionToken::normal()));
    }
}
