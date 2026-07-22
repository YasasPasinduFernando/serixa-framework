<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Assets\StyleSheet;
use Serixa\Component\Accordion;
use Serixa\Component\AccordionItem;
use Serixa\Component\Badge;
use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\Dropdown;
use Serixa\Component\DropdownItem;
use Serixa\Component\Modal;
use Serixa\Component\Progress;
use Serixa\Component\SizedBox;
use Serixa\Component\Spinner;
use Serixa\Component\Tab;
use Serixa\Component\Tabs;
use Serixa\Component\Text;
use Serixa\Component\Toast;
use Serixa\Document\Document;
use Serixa\Rendering\Renderer;
use Serixa\Runtime\ClientRuntime;

$page = Column::make([
    Text::make('Interactive components')->as('h1')->size('2xl')->weight('bold'),
    Text::make('SSR markup with optional serixa.js modules.')->tone('muted'),
    SizedBox::height(3),

    Text::make('Badge & loading')->as('h2')->size('lg')->weight('semibold'),
    Badge::make('v0.9')->color('primary'),
    Spinner::make()->size('md')->label('Loading'),
    Progress::make()->value(65)->max(100)->label('Upload'),
    SizedBox::height(3),

    Text::make('Dropdown')->as('h2')->size('lg')->weight('semibold'),
    Dropdown::make()
        ->trigger(Button::make('Account'))
        ->items([
            DropdownItem::make('Profile')->href('#'),
            DropdownItem::make('Settings')->href('#'),
            DropdownItem::make('Sign out')->danger(),
        ]),
    SizedBox::height(3),

    Text::make('Tabs')->as('h2')->size('lg')->weight('semibold'),
    Tabs::make()
        ->tab(Tab::make('General')->content(Text::make('General settings panel.'))->active())
        ->tab(Tab::make('Security')->content(Text::make('Security settings panel.'))),
    SizedBox::height(3),

    Text::make('Accordion')->as('h2')->size('lg')->weight('semibold'),
    Accordion::make()
        ->item(
            AccordionItem::make()
                ->title('What is SERIXA?')
                ->content(Text::make('A PHP-first UI framework with progressive enhancement.'))
                ->open(true),
        )
        ->item(
            AccordionItem::make()
                ->title('Is JavaScript required?')
                ->content(Text::make('No. Modules enhance markup that already works.')),
        ),
    SizedBox::height(3),

    Text::make('Toast')->as('h2')->size('lg')->weight('semibold'),
    Toast::make()->message('Changes saved')->tone('success')->open(true),
    SizedBox::height(3),

    Text::make('Modal (closed by default — open via runtime)')->as('h2')->size('lg')->weight('semibold'),
    Button::make('Open modal')
        ->primary()
        ->attribute('data-serixa-open-modal', 'demo-modal'),
    Modal::make()
        ->id('demo-modal')
        ->title('Confirm action')
        ->body(Text::make('This dialog is progressive-enhancement ready.'))
        ->footer(Button::make('Close')->ghost()->attribute('data-serixa-close', true))
        ->open(false),
])->gap(2);

$document = Document::make()
    ->title('Interactive · SERIXA')
    ->body($page);

StyleSheet::register($document->assets());
ClientRuntime::register($document->assets());

echo (new Renderer())->render($document), PHP_EOL;
