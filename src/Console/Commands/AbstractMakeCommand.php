<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

use Serixa\Console\Command;
use Serixa\Console\InputOutput;
use Serixa\Console\Scaffold\NameInflector;
use Serixa\Console\Scaffold\TemplateEngine;
use Serixa\Console\Scaffold\WorkingDirectory;
use Serixa\Exception\InvalidArgumentException;

/**
 * Shared make:* scaffolding behaviour.
 */
abstract class AbstractMakeCommand extends Command
{
    public function __construct(
        protected readonly TemplateEngine $templates,
    ) {
    }

    abstract protected function stubPath(): string;

    abstract protected function targetDirectory(): string;

    abstract protected function classSuffix(): string;

    abstract protected function baseNamespace(): string;

    /**
     * @param array<string, string|bool> $options
     */
    public function handle(array $arguments, array $options, InputOutput $io): int
    {
        $raw = $this->requireArgument($arguments, 0, 'name', $io);

        if ($raw === null) {
            return 1;
        }

        $class = NameInflector::className($raw, $this->classSuffix());
        $relativeNs = NameInflector::relativeNamespace($raw);
        $namespace = $this->baseNamespace() . ($relativeNs !== '' ? '\\' . $relativeNs : '');
        $subdir = $relativeNs !== '' ? str_replace('\\', DIRECTORY_SEPARATOR, $relativeNs) . DIRECTORY_SEPARATOR : '';
        $destination = WorkingDirectory::get()
            . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $this->targetDirectory())
            . DIRECTORY_SEPARATOR . $subdir . $class . '.php';

        try {
            $this->templates->writeStub($this->stubPath(), $destination, [
                'ClassName' => $class,
                'Namespace' => $namespace,
                'Name' => preg_replace('/' . preg_quote($this->classSuffix(), '/') . '$/', '', $class) ?? $class,
            ]);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            if (str_contains($e->getMessage(), 'already exists')) {
                $io->info('Duplicate generation blocked — rename the target or delete the existing file.');
            }

            return 1;
        }

        $io->success(sprintf('Created %s', $destination));
        $io->info(sprintf('Namespace: %s\\%s', $namespace, $class));

        return 0;
    }
}
