<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\Form;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\SearchField;
use Serixa\Component\SizedBox;
use Serixa\Component\Text;
use Serixa\Document\Document;
use Serixa\Event\EventRegistry;
use Serixa\Rendering\Renderer;
use Serixa\Runtime\ClientRuntime;

$registry = (new EventRegistry())
    ->register('login', 'submit', 'Demo login form')
    ->register('search', 'input', 'Demo search field')
    ->register('saveForm', 'click', 'Demo save button');

$page = Column::make([
    Text::make('Progressive enhancement')->as('h1')->size('2xl')->weight('bold'),
    Text::make('Event metadata only — no business logic.')->tone('muted'),
    SizedBox::height(3),
    Form::make()
        ->method('POST')
        ->action('/login')
        ->onSubmit('login')
        ->child(
            Label::make('Email')->for('email'),
            Input::make()
                ->id('email')
                ->name('email')
                ->type('email')
                ->required()
                ->autocomplete('email')
                ->maxLength(255),
            Button::make('Sign in')->primary()->type('submit')->onClick('saveForm'),
        ),
    SizedBox::height(4),
    SearchField::make()->name('q')->placeholder('Search…')->onInput('search'),
    SizedBox::height(2),
    Text::make('Registered handlers: ' . implode(', ', $registry->names()))->size('sm')->tone('muted'),
])->gap(2);

$document = Document::make()->title('Events · SERIXA')->body($page);
$document->assets()->js('https://cdn.tailwindcss.com');
ClientRuntime::register($document->assets());

echo (new Renderer())->render($document), PHP_EOL;
