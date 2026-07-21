<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

final class MakePageCommand extends AbstractMakeCommand
{
    public function name(): string
    {
        return 'make:page';
    }

    public function description(): string
    {
        return 'Generate a page class under pages/';
    }

    protected function stubPath(): string
    {
        return 'page/Page.stub';
    }

    protected function targetDirectory(): string
    {
        return 'pages';
    }

    protected function classSuffix(): string
    {
        return 'Page';
    }

    protected function baseNamespace(): string
    {
        return 'App\\Pages';
    }
}
