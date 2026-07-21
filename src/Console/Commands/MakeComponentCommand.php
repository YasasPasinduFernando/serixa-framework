<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

final class MakeComponentCommand extends AbstractMakeCommand
{
    public function name(): string
    {
        return 'make:component';
    }

    public function description(): string
    {
        return 'Generate a component class under components/';
    }

    protected function stubPath(): string
    {
        return 'component/Component.stub';
    }

    protected function targetDirectory(): string
    {
        return 'components';
    }

    protected function classSuffix(): string
    {
        return '';
    }

    protected function baseNamespace(): string
    {
        return 'App\\Components';
    }
}
