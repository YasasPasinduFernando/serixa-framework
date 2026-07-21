<?php

declare(strict_types=1);

namespace Serixa\Console\Commands;

use Serixa\Console\Command;
use Serixa\Console\InputOutput;
use Serixa\Console\Scaffold\WorkingDirectory;
use Serixa\Registry\WidgetRegistry;
use Serixa\Theme\DefaultTheme;

/**
 * serixa docs — generate developer documentation into docs/generated/.
 */
final class DocsCommand extends Command
{
    public function __construct(
        private readonly string $frameworkRoot,
    ) {
    }

    public function name(): string
    {
        return 'docs';
    }

    public function description(): string
    {
        return 'Generate widget, theme, API, and examples documentation';
    }

    public function handle(array $arguments, array $options, InputOutput $io): int
    {
        $cwd = WorkingDirectory::get();
        $outDir = $cwd . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'generated';

        if (! is_dir($outDir) && ! mkdir($outDir, 0777, true) && ! is_dir($outDir)) {
            $io->error(sprintf('Unable to create %s', $outDir));

            return 1;
        }

        $registry = WidgetRegistry::withDefaults();
        $widgets = [];

        foreach ($registry->all() as $meta) {
            $widgets[] = sprintf(
                '| `%s` | %s | %s | %s |',
                $meta->name(),
                $meta->category(),
                $meta->description(),
                $meta->className() !== '' ? '`' . $meta->className() . '`' : '—',
            );
        }

        $widgetDoc = "# Generated widget catalog\n\n"
            . "| Name | Category | Description | Class |\n|------|----------|-------------|-------|\n"
            . implode("\n", $widgets) . "\n";

        file_put_contents($outDir . DIRECTORY_SEPARATOR . 'widgets.md', $widgetDoc);

        $theme = new DefaultTheme();
        $themeDoc = "# Generated theme notes\n\n"
            . "Active default theme: `Serixa\\Theme\\DefaultTheme`\n\n"
            . "Tokens are resolved through `ThemeInterface::token()` / `classes()`.\n"
            . 'Sample button primary classes: `' . implode(' ', $theme->buttonPrimary()) . "`\n";

        file_put_contents($outDir . DIRECTORY_SEPARATOR . 'themes.md', $themeDoc);

        $components = "# Component list\n\n"
            . implode("\n", array_map(static fn ($m) => '- `' . $m->name() . '`', $registry->all())) . "\n";
        file_put_contents($outDir . DIRECTORY_SEPARATOR . 'components.md', $components);

        $api = "# Public API surface (generated)\n\n"
            . "- Namespace: `Serixa\\`\n"
            . "- Widgets: see widgets.md\n"
            . "- CLI: `Serixa\\Console\\Application`\n"
            . "- Runtime: `assets/runtime/serixa.js`\n";
        file_put_contents($outDir . DIRECTORY_SEPARATOR . 'api.md', $api);

        $examplesRoot = $this->frameworkRoot . DIRECTORY_SEPARATOR . 'examples';
        $exampleFiles = is_dir($examplesRoot) ? (glob($examplesRoot . DIRECTORY_SEPARATOR . '*.php') ?: []) : [];
        $examples = "# Examples index\n\n";

        foreach ($exampleFiles as $file) {
            $examples .= '- `' . basename($file) . "`\n";
        }

        file_put_contents($outDir . DIRECTORY_SEPARATOR . 'examples.md', $examples);
        file_put_contents($outDir . DIRECTORY_SEPARATOR . '.cache.json', json_encode([
            'generated_at' => gmdate('c'),
            'widget_count' => count($registry->all()),
            'examples' => array_map('basename', $exampleFiles),
        ], JSON_PRETTY_PRINT));

        $io->success(sprintf('Documentation written to %s', $outDir));

        return 0;
    }
}
