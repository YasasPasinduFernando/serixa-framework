<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Component\Button;
use Serixa\Component\Center;
use Serixa\Component\Column;
use Serixa\Component\Text;
use Serixa\Document\Asset\AssetBundle;
use Serixa\Document\Document;
use Serixa\Rendering\Renderer;

$document = Document::make()
    ->title('Dashboard')
    ->description('SERIXA Admin')
    ->keywords(['dashboard', 'admin'])
    ->themeColor('#2563eb')
    ->language('en')
    ->canonical('https://example.com/dashboard')
    ->favicon('/favicon.ico')
    ->openGraph('og:type', 'website')
    ->body(
        Center::make(
            Column::make([
                Text::make('Hello')->as('h1')->size('xl')->weight('bold'),
                Text::make('Welcome to the document engine.')->tone('muted'),
                Button::make('Continue')->primary()->large(),
            ])->gap(4)->alignCenter(),
        ),
    );

$document->assets()
    ->bundle(
        (new AssetBundle('app'))
            ->css('/css/app.css')
            ->js('/js/app.js', ['defer' => true]),
    )
    ->preload('/fonts/Inter.woff2', 'font');

echo (new Renderer())->render($document), PHP_EOL;
