<?php

declare(strict_types=1);

namespace Serixa\Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Serixa\Component\Button;
use Serixa\Component\Center;
use Serixa\Component\Column;
use Serixa\Component\Component;
use Serixa\Component\Text;
use Serixa\Contracts\ComponentInterface;
use Serixa\Contracts\WidgetInterface;
use Serixa\Event\ClickEvent;
use Serixa\Event\NullWidgetEventDispatcher;
use Serixa\Rendering\Renderer;
use Serixa\State\StateBuilder;
use Serixa\State\StateManager;
use Serixa\State\StateResolver;
use Serixa\State\WidgetState;
use Serixa\Token\ColorToken;
use Serixa\Tree\TreeWalker;
use Serixa\Tree\WidgetTree;
use Serixa\Widget\Widget;
use Serixa\Widget\WidgetCollection;
use Serixa\Widget\WidgetFactory;

final class FoundationTest extends TestCase
{
    protected function tearDown(): void
    {
        StateManager::reset();
        parent::tearDown();
    }

    public function testComponentIsBackwardCompatibleAliasOfWidget(): void
    {
        $button = Button::make('OK');

        $this->assertInstanceOf(WidgetInterface::class, $button);
        $this->assertInstanceOf(ComponentInterface::class, $button);
        $this->assertInstanceOf(Component::class, $button);
        $this->assertInstanceOf(Widget::class, $button);
    }

    public function testWidgetTreeCapturesNestedStructure(): void
    {
        $root = Center::make(
            Column::make([
                Text::make('Welcome'),
                Button::make('Go'),
            ]),
        );

        $tree = WidgetTree::from($root);

        $this->assertTrue($tree->root()->isWidget());
        $this->assertCount(1, $tree->root()->children());
        $this->assertCount(2, $tree->root()->children()[0]->children());
    }

    public function testRendererRunsLifecycleHooks(): void
    {
        $widget = new class () extends Component {
            public array $calls = [];

            public function beforeMount(): void
            {
                $this->calls[] = 'beforeMount';
            }

            public function mounted(): void
            {
                parent::mounted();
                $this->calls[] = 'mounted';
            }

            public function beforeRender(): void
            {
                $this->calls[] = 'beforeRender';
            }

            public function afterRender(): void
            {
                $this->calls[] = 'afterRender';
            }

            public function render(): string
            {
                return '<div>ok</div>';
            }
        };

        $html = (new Renderer())->render($widget);

        $this->assertSame('<div>ok</div>', $html);
        $this->assertSame(
            ['beforeMount', 'mounted', 'beforeRender', 'afterRender'],
            $widget->calls,
        );
        $this->assertTrue($widget->isMounted());
    }

    public function testStateManagerIsMemoryOnly(): void
    {
        $manager = StateManager::current();
        $manager->get('modal')->set('open', true);

        $this->assertTrue($manager->get('modal')->get('open'));
        $this->assertInstanceOf(WidgetState::class, $manager->get('modal'));

        $built = StateBuilder::make()->set('tab', 2)->toState();
        $manager->put('tabs', $built);
        $this->assertSame(2, $manager->get('tabs')->get('tab'));

        $button = Button::make('X')->stateKey('btn');
        $resolver = new StateResolver(StateManager::current());
        $resolver->resolve($button)->set('pressed', true);
        $this->assertTrue(StateManager::current()->get('btn')->get('pressed'));
    }

    public function testWidgetFactoryCreatesRegisteredWidgets(): void
    {
        $factory = new WidgetFactory();

        $this->assertTrue($factory->has('button'));
        $this->assertInstanceOf(Button::class, $factory->make('button', 'Save'));
        $this->assertInstanceOf(Text::class, $factory->make('text', 'Hi'));
    }

    public function testWidgetCollection(): void
    {
        $collection = new WidgetCollection([
            Text::make('A'),
            Button::make('B'),
        ]);

        $this->assertCount(2, $collection);
        $this->assertFalse($collection->isEmpty());
    }

    public function testAccessibilityBuilderMergesIntoRender(): void
    {
        $button = Button::make('Menu');
        $button->accessibility()->role('button')->expanded(false)->label('Open menu');

        $html = $button->render();

        $this->assertStringContainsString('role="button"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('aria-label="Open menu"', $html);
    }

    public function testEventDispatcherStub(): void
    {
        $dispatcher = new NullWidgetEventDispatcher();
        $seen = [];

        $dispatcher->listen('click', function ($event) use (&$seen): void {
            $seen[] = $event->name();
        });

        $dispatcher->dispatch(new ClickEvent());

        $this->assertSame(['click'], $seen);
    }

    public function testTreeWalkerVisitsAllNodes(): void
    {
        $visited = 0;
        $tree = WidgetTree::from(
            Column::make([Text::make('A'), Text::make('B')]),
        );

        (new TreeWalker())->walk($tree->root(), new class ($visited) implements \Serixa\Tree\TreeVisitor {
            public function __construct(private int &$visited)
            {
            }

            public function enter(\Serixa\Tree\TreeNode $node): void
            {
                if ($node->isWidget()) {
                    $this->visited++;
                }
            }

            public function leave(\Serixa\Tree\TreeNode $node): void
            {
            }
        });

        $this->assertSame(3, $visited);
    }

    public function testColorTokenNames(): void
    {
        $this->assertSame('color.primary', ColorToken::primary()->name());
        $this->assertSame('color.textMuted', (string) ColorToken::textMuted());
    }
}
