<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

final class MakeThemeCommand extends AbstractMakeCommand
{
    public function name(): string
    {
        return 'make:theme';
    }

    public function description(): string
    {
        return 'Generate a theme class under themes/';
    }

    protected function stubPath(): string
    {
        return 'theme/Theme.stub';
    }

    protected function targetDirectory(): string
    {
        return 'themes';
    }

    protected function classSuffix(): string
    {
        return 'Theme';
    }

    protected function baseNamespace(): string
    {
        return 'App\\Themes';
    }
}
