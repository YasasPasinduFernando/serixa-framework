<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

use Serixa\Console\Command;
use Serixa\Console\InputOutput;
use Serixa\Console\Scaffold\TemplateEngine;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Runtime\ClientRuntime;

/**
 * serixa new {name} — generate a project skeleton (blog|crm|ecommerce presets).
 */
final class NewProjectCommand extends Command
{
    /** @var list<string> */
    private const PRESETS = ['blog', 'crm', 'ecommerce'];

    public function __construct(
        private readonly TemplateEngine $templates,
        private readonly string $frameworkRoot,
    ) {
    }

    public function name(): string
    {
        return 'new';
    }

    public function description(): string
    {
        return 'Create a new SERIXA application (blog, crm, ecommerce, or custom name)';
    }

    public function handle(array $arguments, array $options, InputOutput $io): int
    {
        $name = $this->requireArgument($arguments, 0, 'name', $io);

        if ($name === null) {
            return 1;
        }

        $preset = in_array(strtolower($name), self::PRESETS, true)
            ? strtolower($name)
            : (string) ($options['type'] ?? 'blog');

        if (! in_array($preset, self::PRESETS, true)) {
            $io->error(sprintf('Unknown project type "%s". Allowed: %s', $preset, implode(', ', self::PRESETS)));

            return 1;
        }

        $target = getcwd() . DIRECTORY_SEPARATOR . $name;

        if (is_dir($target) || is_file($target)) {
            $io->error(sprintf('Path already exists: %s', $target));
            $io->info('Choose a different name, or remove the existing folder first.');

            return 1;
        }

        $cwd = realpath(getcwd() ?: '.') ?: (getcwd() ?: '.');
        $framework = realpath($this->frameworkRoot) ?: $this->frameworkRoot;

        if ($this->isInsideFrameworkTree($cwd, $framework)) {
            $io->info('Warning: creating a project inside the SERIXA framework tree.');
            $io->info('Composer path repositories may recurse through nested vendor folders.');
            $io->info('Prefer generating apps in a sibling directory outside the framework clone.');
        }

        $namespace = 'App';
        $title = ucfirst($preset) . ' · ' . $name;

        try {
            $this->templates->publish('project/base', $target, [
                'ProjectName' => $name,
                'ProjectTitle' => $title,
                'Preset' => $preset,
                'Namespace' => $namespace,
                'FrameworkPath' => $this->relativeFrameworkPath($target),
            ]);

            $presetDir = 'project/' . $preset;

            if (is_dir($this->templates->root() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $presetDir))) {
                $this->templates->publish($presetDir, $target, [
                    'ProjectName' => $name,
                    'ProjectTitle' => $title,
                    'Preset' => $preset,
                    'Namespace' => $namespace,
                ]);
            }

            $this->publishRuntime($target);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        $io->success(sprintf('Created SERIXA %s project at %s', $preset, $target));
        $io->info('Next:');
        $io->info(sprintf('  cd %s', $name));
        $io->info('  composer install');
        $io->info('  php -S localhost:8000 -t public');
        $io->info('Then open http://localhost:8000 — see docs/getting-started.md');

        return 0;
    }

    private function isInsideFrameworkTree(string $cwd, string $framework): bool
    {
        $cwdNorm = strtolower(str_replace('\\', '/', rtrim($cwd, '\\/')));
        $fwNorm = strtolower(str_replace('\\', '/', rtrim($framework, '\\/')));

        return $cwdNorm === $fwNorm || str_starts_with($cwdNorm, $fwNorm . '/');
    }

    private function relativeFrameworkPath(string $projectPath): string
    {
        $framework = realpath($this->frameworkRoot) ?: $this->frameworkRoot;

        return str_replace('\\', '/', $framework);
    }

    private function publishRuntime(string $target): void
    {
        $destDir = $target . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'runtime';

        if (! is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $src = ClientRuntime::scriptPath();

        if (is_file($src)) {
            copy($src, $destDir . DIRECTORY_SEPARATOR . 'serixa.js');
        }
    }
}
