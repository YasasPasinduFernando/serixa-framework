<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

use Serixa\Console\Command;
use Serixa\Console\InputOutput;
use Serixa\Console\Scaffold\WorkingDirectory;
use Serixa\Runtime\ClientRuntime;

/**
 * serixa build — compile tokens, runtime assets, manifest, docs cache.
 */
final class BuildCommand extends Command
{
    public function __construct(
        private readonly string $frameworkRoot,
    ) {
    }

    public function name(): string
    {
        return 'build';
    }

    public function description(): string
    {
        return 'Build theme tokens, runtime assets, manifest, and docs cache';
    }

    public function handle(array $arguments, array $options, InputOutput $io): int
    {
        $cwd = WorkingDirectory::get();
        $buildDir = $cwd . DIRECTORY_SEPARATOR . 'build';

        if (! is_dir($buildDir) && ! mkdir($buildDir, 0777, true) && ! is_dir($buildDir)) {
            $io->error(sprintf('Unable to create %s', $buildDir));

            return 1;
        }

        $tokens = [
            'colors' => ['primary', 'secondary', 'success', 'warning', 'danger', 'muted'],
            'spacing' => [0, 1, 2, 3, 4, 6, 8],
            'radius' => ['sm', 'md', 'lg', 'full'],
            'generated_at' => gmdate('c'),
        ];
        file_put_contents($buildDir . DIRECTORY_SEPARATOR . 'tokens.json', json_encode($tokens, JSON_PRETTY_PRINT));

        $runtimeSrc = ClientRuntime::scriptPath();
        $runtimeDestDir = $cwd . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'runtime';

        if (! is_dir($runtimeDestDir)) {
            mkdir($runtimeDestDir, 0777, true);
        }

        if (is_file($runtimeSrc)) {
            copy($runtimeSrc, $runtimeDestDir . DIRECTORY_SEPARATOR . 'serixa.js');
        }

        $manifest = [
            'version' => \Serixa\Console\Application::VERSION,
            'framework_root' => $this->frameworkRoot,
            'tokens' => 'build/tokens.json',
            'runtime' => 'public/assets/runtime/serixa.js',
            'built_at' => gmdate('c'),
        ];
        file_put_contents($buildDir . DIRECTORY_SEPARATOR . 'manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        // Refresh docs cache when present / create lightweight cache.
        (new DocsCommand($this->frameworkRoot))->handle([], [], new InputOutput(true));

        $io->success('Build complete.');
        $io->info('  build/tokens.json');
        $io->info('  build/manifest.json');
        $io->info('  public/assets/runtime/serixa.js');

        return 0;
    }
}
