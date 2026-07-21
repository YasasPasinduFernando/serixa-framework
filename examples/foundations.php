<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\Text;
use Serixa\Rendering\Renderer;
use Serixa\State\StateManager;
use Serixa\Theme\ThemeManager;
use Serixa\Token\ColorToken;

$primary = ThemeManager::current()->token(ColorToken::primary());
StateManager::current()->get('demo')->set('clicks', 0);

$ui = Column::make([
    Text::make('Foundations demo')->as('h1')->size('xl')->weight('bold'),
    Text::make('Primary token classes: ' . implode(' ', $primary))->tone('muted')->as('p'),
    Button::make('Continue')
        ->primary()
        ->large()
        ->stateKey('demo'),
])->gap(4)->alignCenter();

$renderer = new Renderer();
$tree = $renderer->tree($ui);

echo 'Tree child count: ' . count($tree->root()->children()), PHP_EOL;
echo $renderer->render($ui), PHP_EOL;
