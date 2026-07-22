<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Component\Button;
use Serixa\Component\Checkbox;
use Serixa\Component\Column;
use Serixa\Component\Form;
use Serixa\Component\Input;
use Serixa\Component\Label;
use Serixa\Component\PasswordInput;
use Serixa\Component\Select;
use Serixa\Component\SwitchControl;
use Serixa\Component\Text;
use Serixa\Component\Textarea;
use Serixa\Document\Document;
use Serixa\Rendering\Renderer;
use Serixa\Styling\StyleSheet;

$form = Form::make()
    ->method('POST')
    ->action('/login')
    ->child(
        Column::make([
            Text::make('Sign in')->as('h1')->size('xl')->weight('bold'),
            Label::make('Email')->for('email'),
            Input::make()
                ->id('email')
                ->name('email')
                ->type('email')
                ->placeholder('Enter your email')
                ->required(),
            Label::make('Password')->for('password'),
            PasswordInput::make()
                ->id('password')
                ->name('password')
                ->required(),
            Checkbox::make()
                ->name('remember')
                ->label('Remember me'),
            Label::make('Role')->for('role'),
            Select::make()
                ->id('role')
                ->name('role')
                ->placeholder('Choose a role')
                ->options([
                    'admin' => 'Admin',
                    'editor' => 'Editor',
                    'viewer' => 'Viewer',
                ]),
            Label::make('Bio')->for('bio'),
            Textarea::make()->id('bio')->name('bio')->rows(3)->placeholder('Optional bio'),
            SwitchControl::make()
                ->name('newsletter')
                ->label('Subscribe to product updates'),
            Button::make('Login')->primary()->type('submit'),
        ])->gap(3),
    );

$document = Document::make()
    ->title('Forms · SERIXA')
    ->body($form);

StyleSheet::register($document->assets());

echo (new Renderer())->render($document), PHP_EOL;
