<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

use Serixa\Console\Command;
use Serixa\Console\InputOutput;
use Serixa\Console\Scaffold\WorkingDirectory;

/**
 * serixa dev — watch project files and rebuild (no browser live-reload yet).
 */
final class DevCommand extends Command
{
    public function __construct(
        private readonly string $frameworkRoot,
    ) {
    }

    public function name(): string
    {
        return 'dev';
    }

    public function description(): string
    {
        return 'Watch project files and rebuild assets / docs cache';
    }

    public function handle(array $arguments, array $options, InputOutput $io): int
    {
        $cwd = WorkingDirectory::get();
        $once = isset($options['once']) && $options['once'];
        $interval = isset($options['interval']) ? max(1, (int) $options['interval']) : 1;
        $maxIterations = $once ? 1 : (isset($options['max']) ? max(1, (int) $options['max']) : PHP_INT_MAX);

        $io->info(sprintf('SERIXA dev watcher in %s (interval=%ds)', $cwd, $interval));
        $io->info('Browser live-reload is reserved for a later milestone.');

        $snapshot = $this->snapshot($cwd);
        $iterations = 0;

        while ($iterations < $maxIterations) {
            ++$iterations;

            if ($iterations > 1 || $once) {
                $current = $this->snapshot($cwd);

                if ($current !== $snapshot || $once) {
                    $io->info(sprintf('[%s] change detected — rebuilding', gmdate('H:i:s')));
                    (new BuildCommand($this->frameworkRoot))->handle([], [], $io);
                    $snapshot = $current;
                }
            }

            if ($once) {
                break;
            }

            sleep($interval);
        }

        return 0;
    }

    /**
     * @return array<string, int>
     */
    private function snapshot(string $cwd): array
    {
        $map = [];
        $roots = ['pages', 'components', 'layouts', 'themes', 'plugins', 'config', 'resources', 'public'];

        foreach ($roots as $root) {
            $path = $cwd . DIRECTORY_SEPARATOR . $root;

            if (! is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            );

            /** @var \SplFileInfo $file */
            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $map[$file->getPathname()] = (int) $file->getMTime();
            }
        }

        ksort($map);

        return $map;
    }
}
