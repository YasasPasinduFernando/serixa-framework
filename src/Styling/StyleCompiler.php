<?php

declare(strict_types=1);

namespace Serixa\Styling;

/**
 * End-to-end stylesheet compilation for a project.
 *
 * Pipeline: ThemeCompiler → CssOptimizer → utilities foundation → app CSS → CssWriter.
 */
final class StyleCompiler
{
    public function __construct(
        private readonly ThemeCompiler $themeCompiler = new ThemeCompiler(),
        private readonly CssOptimizer $optimizer = new CssOptimizer(),
        private readonly CssWriter $writer = new CssWriter(),
    ) {
    }

    /**
     * @return array{
     *   path: string,
     *   bytes: int,
     *   sources: list<string>,
     *   ms: float,
     *   manifest: ThemeManifest,
     *   rules: int
     * }
     */
    public function compile(string $projectRoot, string $outputRelative = 'public/assets/serixa.css'): array
    {
        $started = microtime(true);
        $projectRoot = rtrim($projectRoot, '/\\');
        $output = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outputRelative);

        $sources = [];
        $chunks = [];

        // 1) Theme tokens + semantic .sx-* recipes
        $rules = $this->optimizer->optimize($this->themeCompiler->compile());
        $themeCss = $this->writer->stringify($rules, 'SERIXA theme compiler');
        $chunks[] = $themeCss;
        $sources[] = 'theme:compiler';

        // 2) Utility foundation (committed offline CSS — DefaultTheme class maps)
        $foundation = StyleSheet::foundationPath();
        if (is_file($foundation)) {
            $chunks[] = (string) file_get_contents($foundation);
            $sources[] = StyleSheet::FOUNDATION_RELATIVE;
        }

        // 3) App overrides
        foreach (StyleSheet::discoverAppSources($projectRoot) as $relative => $absolute) {
            $chunks[] = "\n/* --- {$relative} --- */\n" . (string) file_get_contents($absolute);
            $sources[] = $relative;
        }

        $css = $this->optimizer->dedupeCssChunks(implode("\n", $chunks));
        $bytes = $this->writer->write($output, $css);

        // Mirror into build/ for tooling
        $buildCss = $projectRoot . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'serixa.css';
        $this->writer->write($buildCss, $css);

        // BC alias: older pipelines looked for app.css
        $alias = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'app.css';
        $this->writer->write($alias, $css);

        $ms = round((microtime(true) - $started) * 1000, 2);
        $selectors = array_map(
            static fn (StyleRule $rule): string => $rule->selector()->value(),
            $rules,
        );

        $manifest = new ThemeManifest(
            version: '1.0.0',
            selectors: $selectors,
            tokens: $this->themeCompiler->tokenNames(),
            sources: $sources,
            sizes: [
                'serixa.css' => $bytes,
                'theme' => strlen($themeCss),
            ],
            builtAt: gmdate('c'),
            compileMs: $ms,
        );

        $manifest->write($projectRoot . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'theme-manifest.json');
        $manifest->write($projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'theme-manifest.json');

        return [
            'path' => $output,
            'bytes' => $bytes,
            'sources' => $sources,
            'ms' => $ms,
            'manifest' => $manifest,
            'rules' => count($rules),
        ];
    }
}
