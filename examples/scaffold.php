<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Assets\StyleSheet;
use Serixa\Component\AppBar;
use Serixa\Component\Button;
use Serixa\Component\Divider;
use Serixa\Component\Footer;
use Serixa\Component\Link;
use Serixa\Component\MainContent;
use Serixa\Component\Scaffold;
use Serixa\Component\Sidebar;
use Serixa\Component\Text;
use Serixa\Document\Document;
use Serixa\Rendering\Renderer;

$shell = Scaffold::make()
    ->appBar(
        AppBar::make()
            ->title('Dashboard')
            ->actions(Button::make('Logout')->ghost()->small()),
    )
    ->sidebar(
        Sidebar::make(
            Link::make('Dashboard')->href('/')->active(),
            Link::make('Settings')->href('/settings'),
            Divider::make()->spacing('sm'),
            Link::make('Profile')->href('/profile'),
        )->ariaLabel('Primary'),
    )
    ->body(
        MainContent::make(
            Text::make('Welcome back')->as('h1')->size('2xl')->weight('bold'),
            Text::make('UI Foundation chrome widgets compose through Scaffold.')
                ->tone('muted'),
        ),
    )
    ->footer(
        Footer::make('© 2026 SERIXA'),
    );

$document = Document::make()
    ->title('Scaffold · SERIXA')
    ->body($shell);

StyleSheet::register($document->assets());

echo (new Renderer())->render($document), PHP_EOL;
