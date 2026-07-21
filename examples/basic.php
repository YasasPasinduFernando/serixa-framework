<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Component\Button;
use Serixa\Component\Card;
use Serixa\Component\Center;
use Serixa\Component\Column;
use Serixa\Component\Container;
use Serixa\Component\SizedBox;
use Serixa\Component\Text;
use Serixa\Rendering\Renderer;

$ui = Container::make(
    Center::make(
        Column::make([
            Card::make()
                ->title('SERIXA')
                ->child(
                    Text::make('Write PHP. Get semantic HTML.')
                        ->tone('muted'),
                )
                ->child(SizedBox::height(4))
                ->child(
                    Button::make('Get started')
                        ->primary()
                        ->large(),
                ),
        ])->alignCenter()->gap(4),
    ),
)->maxWidth('md');

echo (new Renderer())->render($ui), PHP_EOL;
