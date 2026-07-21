<?php

declare(strict_types=1);

namespace Serixa\Tests\Runtime;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Text;
use Serixa\Container\Container;
use Serixa\Contracts\ThemeInterface;
use Serixa\Exception\PipelineException;
use Serixa\Pipeline\Pass\BuildTreePass;
use Serixa\Pipeline\RenderPipeline;
use Serixa\Plugin\PluginManager;
use Serixa\Plugin\WidgetPlugin;
use Serixa\Registry\WidgetMetadata;
use Serixa\Registry\WidgetRegistry;
use Serixa\Rendering\HtmlWriter;
use Serixa\Rendering\Renderer;
use Serixa\Runtime\RenderContext;
use Serixa\Runtime\WidgetContext;
use Serixa\Theme\DefaultTheme;
use Serixa\Theme\ThemeManager;
use Serixa\Token\DesignToken;

final class RuntimeCoreTest extends TestCase
{
    protected function tearDown(): void
    {
        ThemeManager::reset();
        parent::tearDown();
    }

    public function testWidgetContextIsImmutableAndFeatureAware(): void
    {
        $context = WidgetContext::defaults()
            ->withLocale('fr')
            ->withColorMode('dark')
            ->withBreakpoint('lg')
            ->withTextDirection('rtl')
            ->withEnvironment('testing')
            ->withFeatureFlags(['beta' => true]);

        $this->assertSame('fr', $context->locale());
        $this->assertSame('dark', $context->colorMode());
        $this->assertSame('lg', $context->breakpoint());
        $this->assertSame('rtl', $context->textDirection());
        $this->assertTrue($context->hasFeature('beta'));
        $this->assertFalse($context->hasFeature('missing'));
    }

    public function testRenderContextTracksTreeHtmlAndMetadata(): void
    {
        $context = new RenderContext(debug: true, ssr: true);
        $button = Button::make('Go');
        $context->setRoot($button);
        $context->setMeta('k', 'v');
        $context->appendHtml('<b>x</b>');
        $context->incrementDepth();

        $this->assertTrue($context->isDebug());
        $this->assertTrue($context->isSsr());
        $this->assertSame(1, $context->depth());
        $this->assertSame('v', $context->getMeta('k'));
        $this->assertSame('<b>x</b>', $context->html());
        $this->assertNotSame('', $context->renderId());
        $this->assertSame($button, $context->root());
    }

    public function testContainerBindSingletonInstanceAndAutowire(): void
    {
        $container = new Container();
        $container->instance(ThemeInterface::class, new DefaultTheme());
        $container->singleton('counter', static function (): object {
            return new class () {
                public int $n = 0;
            };
        });

        $a = $container->resolve('counter');
        $b = $container->resolve('counter');
        $a->n = 3;
        $this->assertSame(3, $b->n);

        $container->bind('transient', static fn (): object => new \stdClass());
        $this->assertNotSame(
            $container->resolve('transient'),
            $container->resolve('transient'),
        );

        $theme = $container->resolve(ThemeInterface::class);
        $this->assertInstanceOf(DefaultTheme::class, $theme);
    }

    public function testWidgetRegistryRegisterFindUnregister(): void
    {
        $registry = WidgetRegistry::withDefaults();

        $this->assertTrue($registry->has('button'));
        $this->assertSame(Button::class, $registry->classFor('button'));
        $this->assertNotNull($registry->find('column'));

        $registry->register(new WidgetMetadata(
            name: 'custom',
            category: 'test',
            description: 'Custom widget',
            acceptsChildren: true,
            versionIntroduced: '0.4.0',
            className: Text::class,
        ));

        $this->assertTrue($registry->has('custom'));
        $registry->unregister('custom');
        $this->assertFalse($registry->has('custom'));
        $this->assertGreaterThan(10, count($registry->all()));
    }

    public function testPipelineDefaultPassesAndRendererUsesPipeline(): void
    {
        $pipeline = RenderPipeline::default();
        $names = array_map(static fn ($pass) => $pass->name(), $pipeline->passes());

        $this->assertSame(
            ['build_tree', 'resolve_context', 'lifecycle', 'theme_resolution', 'accessibility', 'render'],
            $names,
        );

        $renderer = new Renderer();
        $html = $renderer->render(Button::make('Save')->primary());

        $this->assertStringContainsString('Save', $html);
        $this->assertStringContainsString('<button', $html);
        $this->assertCount(6, $renderer->pipeline()->passes());
    }

    public function testRendererAcceptsCustomWidgetContext(): void
    {
        $theme = new class () implements ThemeInterface {
            public function classes(string $component, array $state = []): array
            {
                return ['from-context-theme'];
            }

            public function token(DesignToken|string $token): array
            {
                return [];
            }

            public function buttonPrimary(): array
            {
                return $this->classes('button', ['variant' => 'primary']);
            }

            public function buttonSecondary(): array
            {
                return [];
            }

            public function buttonDanger(): array
            {
                return [];
            }

            public function buttonGhost(): array
            {
                return [];
            }
        };

        $context = WidgetContext::defaults()->withTheme($theme);
        $renderer = new Renderer(null, $context);
        $html = $renderer->renderWidget(Text::make('Hi'));

        $this->assertStringContainsString('from-context-theme', $html);
    }

    public function testHtmlWriterAppendsToContext(): void
    {
        $context = new RenderContext();
        $writer = new HtmlWriter();
        $writer->write($context, '<div>');
        $writer->writeTrusted($context, '</div>');

        $this->assertSame('<div></div>', $context->html());
        $writer->reset($context);
        $this->assertSame('', $context->html());
    }

    public function testPluginManagerRegistersWidgetPlugin(): void
    {
        $manager = new PluginManager(new Container(), WidgetRegistry::withDefaults());

        $plugin = new class () implements WidgetPlugin {
            public function name(): string
            {
                return 'demo-widgets';
            }

            public function boot(Container $container, WidgetRegistry $registry): void
            {
                $registry->register(new WidgetMetadata(
                    name: 'demoBanner',
                    category: 'marketing',
                    description: 'Demo banner',
                    versionIntroduced: '0.4.0',
                    className: Text::class,
                ));
                $container->instance('demo.flag', true);
            }
        };

        $manager->register($plugin);

        $this->assertTrue($manager->has('demo-widgets'));
        $this->assertTrue($manager->registry()->has('demoBanner'));
        $this->assertTrue($manager->container()->resolve('demo.flag'));
    }

    public function testBuildTreePassRequiresRoot(): void
    {
        $this->expectException(PipelineException::class);
        (new BuildTreePass())->handle(new RenderContext());
    }

    public function testExistingApiStillWorksViaPipeline(): void
    {
        $html = (new Renderer())->render(
            Text::make('Hello'),
            Button::make('Click'),
        );

        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('Click', $html);
    }
}
