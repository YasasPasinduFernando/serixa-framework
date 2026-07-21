<?php

declare(strict_types=1);

namespace Serixa\Console\Scaffold;

use Serixa\Exception\InvalidArgumentException;

/**
 * Filesystem template renderer for scaffolding (not the HTML widget renderer).
 *
 * Templates live under /templates and use {{PLACEHOLDER}} tokens.
 * Community packages can ship additional template roots later.
 */
final class TemplateEngine
{
    public function __construct(
        private readonly string $templatesRoot,
    ) {
    }

    public function root(): string
    {
        return $this->templatesRoot;
    }

    /**
     * @param array<string, string> $variables
     */
    public function render(string $relativePath, array $variables): string
    {
        $path = $this->templatesRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (! is_file($path)) {
            throw new InvalidArgumentException(sprintf('Template not found: %s', $relativePath));
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Unable to read template: %s', $relativePath));
        }

        return $this->interpolate($contents, $variables);
    }

    /**
     * Copy a template directory tree into $destination, rendering *.stub files.
     *
     * @param array<string, string> $variables
     */
    public function publish(string $templateDir, string $destination, array $variables): void
    {
        $source = $this->templatesRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $templateDir);

        if (! is_dir($source)) {
            throw new InvalidArgumentException(sprintf('Template directory not found: %s', $templateDir));
        }

        $this->publishRecursive($source, $destination, $variables);
    }

    /**
     * @param array<string, string> $variables
     */
    public function writeStub(string $relativeStub, string $destinationFile, array $variables): void
    {
        if (is_file($destinationFile)) {
            throw new InvalidArgumentException(sprintf('Refusing to overwrite existing file: %s', $destinationFile));
        }

        $dir = dirname($destinationFile);

        if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('Unable to create directory: %s', $dir));
        }

        $rendered = $this->render($relativeStub, $variables);

        if (file_put_contents($destinationFile, $rendered) === false) {
            throw new InvalidArgumentException(sprintf('Unable to write: %s', $destinationFile));
        }
    }

    /**
     * @param array<string, string> $variables
     */
    private function interpolate(string $contents, array $variables): string
    {
        $search = [];
        $replace = [];

        foreach ($variables as $key => $value) {
            $search[] = '{{' . $key . '}}';
            $replace[] = $value;
        }

        return str_replace($search, $replace, $contents);
    }

    /**
     * @param array<string, string> $variables
     */
    private function publishRecursive(string $source, string $destination, array $variables): void
    {
        if (! is_dir($destination) && ! mkdir($destination, 0777, true) && ! is_dir($destination)) {
            throw new InvalidArgumentException(sprintf('Unable to create directory: %s', $destination));
        }

        $items = scandir($source);

        if ($items === false) {
            throw new InvalidArgumentException(sprintf('Unable to read directory: %s', $source));
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $from = $source . DIRECTORY_SEPARATOR . $item;
            $toName = str_ends_with($item, '.stub') ? substr($item, 0, -5) : $item;
            $to = $destination . DIRECTORY_SEPARATOR . $toName;

            if (is_dir($from)) {
                $this->publishRecursive($from, $to, $variables);
                continue;
            }

            if (is_file($to)) {
                throw new InvalidArgumentException(sprintf('Refusing to overwrite existing file: %s', $to));
            }

            if (str_ends_with($item, '.stub')) {
                $contents = file_get_contents($from);

                if ($contents === false) {
                    throw new InvalidArgumentException(sprintf('Unable to read template: %s', $from));
                }

                file_put_contents($to, $this->interpolate($contents, $variables));
            } else {
                if (! copy($from, $to)) {
                    throw new InvalidArgumentException(sprintf('Unable to copy: %s', $from));
                }
            }
        }
    }
}
