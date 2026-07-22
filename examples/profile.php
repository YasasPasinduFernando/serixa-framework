<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Assets\StyleSheet;
use Serixa\Component\Avatar;
use Serixa\Component\Column;
use Serixa\Component\Image;
use Serixa\Component\Logo;
use Serixa\Component\Text;
use Serixa\Document\Document;
use Serixa\Rendering\Renderer;

$page = Column::make([
    Logo::make()->text('SERIXA')->href('/'),
    Avatar::make()->initials('AL')->size('xl')->circle(),
    Text::make('Ada Lovelace')->as('h1')->size('xl')->weight('bold'),
    Image::make()->src('https://placehold.co/320x120')->alt('Cover')->width(320)->height(120),
])->gap(4)->alignCenter();

$document = Document::make()->title('Profile · SERIXA')->body($page);
StyleSheet::register($document->assets());

echo (new Renderer())->render($document), PHP_EOL;
