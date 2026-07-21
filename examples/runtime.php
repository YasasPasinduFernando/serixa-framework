<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\Text;
use Serixa\Container\Container;
use Serixa\Plugin\PluginManager;
use Serixa\Plugin\RuntimePlugin;
use Serixa\Registry\WidgetRegistry;
use Serixa\Rendering\Renderer;
use Serixa\Runtime\WidgetContext;
use Serixa\Theme\DefaultTheme;

$registry = WidgetRegistry::withDefaults();
$container = new Container();
$container->instance(DefaultTheme::class, new DefaultTheme());

$plugins = new PluginManager($container, $registry);
$plugins->register(new class () implements RuntimePlugin {
    public function name(): string
    {
        return 'runtime-demo';
    }

    public function boot(Container $container, WidgetRegistry $registry): void
    {
        $container->instance('app.name', 'SERIXA Runtime');
    }
});

$context = WidgetContext::defaults()
    ->withEnvironment('testing')
    ->withColorMode('light')
    ->withFeatureFlags(['runtime_core' => true]);

$renderer = new Renderer(null, $context);

$ui = Column::make([
    Text::make('Runtime Core')->as('h1')->size('xl')->weight('bold'),
    Text::make('App: ' . $container->resolve('app.name'))->tone('muted'),
    Text::make('Registered widgets: ' . (string) count($registry->all()))->as('p'),
    Button::make('Continue')->primary(),
])->gap(3)->alignCenter();

echo $renderer->render($ui), PHP_EOL;
echo 'Pipeline passes: ' . implode(', ', array_map(
    static fn ($pass) => $pass->name(),
    $renderer->pipeline()->passes(),
)), PHP_EOL;
