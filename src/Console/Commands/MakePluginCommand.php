<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

final class MakePluginCommand extends AbstractMakeCommand
{
    public function name(): string
    {
        return 'make:plugin';
    }

    public function description(): string
    {
        return 'Generate a plugin class under plugins/';
    }

    protected function stubPath(): string
    {
        return 'plugin/Plugin.stub';
    }

    protected function targetDirectory(): string
    {
        return 'plugins';
    }

    protected function classSuffix(): string
    {
        return 'Plugin';
    }

    protected function baseNamespace(): string
    {
        return 'App\\Plugins';
    }
}
