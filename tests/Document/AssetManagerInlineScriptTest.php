<?php

declare(strict_types=1);

namespace Serixa\Tests\Document;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Text;
use Serixa\Document\Asset\AssetManager;
use Serixa\Document\Document;
use Serixa\Document\DocumentRenderer;
use Serixa\Document\Head;

final class AssetManagerInlineScriptTest extends TestCase
{
    public function testInlineScriptIsEmittedOnHead(): void
    {
        $assets = new AssetManager();
        $assets->inlineScript('window.__serixa=1;');
        $head = Head::make();
        $assets->applyTo($head);
        $html = $head->render();
        $this->assertStringContainsString('<script>window.__serixa=1;</script>', $html);
    }

    public function testDocumentRenderIncludesInlineScriptViaAssets(): void
    {
        $doc = Document::make()->title('T')->body(Text::make('Hi'));
        $doc->assets()->inlineScript('/* copy */');
        $html = DocumentRenderer::make()->render($doc);
        $this->assertStringContainsString('/* copy */', $html);
        $this->assertStringContainsString('<title>T</title>', $html);
    }
}
