<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

final class MakeLayoutCommand extends AbstractMakeCommand
{
    public function name(): string
    {
        return 'make:layout';
    }

    public function description(): string
    {
        return 'Generate a layout class under layouts/';
    }

    protected function stubPath(): string
    {
        return 'layout/Layout.stub';
    }

    protected function targetDirectory(): string
    {
        return 'layouts';
    }

    protected function classSuffix(): string
    {
        return 'Layout';
    }

    protected function baseNamespace(): string
    {
        return 'App\\Layouts';
    }
}
