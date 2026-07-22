<?php

declare(strict_types=1);

namespace Serixa\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Serixa\Support\ClassMerger;

final class ClassMergerConflictMatrixTest extends TestCase
{
    protected function tearDown(): void
    {
        (new ClassMerger())->clearCache();
        ClassMerger::clearConflictGroupCache();
        parent::tearDown();
    }

    /**
     * @return array<string, array{0: list<string>, 1: string, 2: list<string>}>
     */
    public static function lastWinsProvider(): array
    {
        return [
            'padding-x' => [['px-2', 'px-4', 'px-8'], 'px-8', ['px-2', 'px-4']],
            'padding-y' => [['py-1', 'py-3'], 'py-3', ['py-1']],
            'margin-x' => [['mx-2', 'mx-auto'], 'mx-auto', ['mx-2']],
            'gap' => [['gap-2', 'gap-4'], 'gap-4', ['gap-2']],
            'width' => [['w-full', 'w-1/2'], 'w-1/2', ['w-full']],
            'flex' => [['flex-1', 'flex-none'], 'flex-none', ['flex-1']],
            'text-size' => [['text-sm', 'text-lg'], 'text-lg', ['text-sm']],
            'bg' => [['bg-white', 'bg-slate-50'], 'bg-slate-50', ['bg-white']],
            'text-color' => [['text-slate-900', 'text-white'], 'text-white', ['text-slate-900']],
            'opacity' => [['opacity-50', 'opacity-100'], 'opacity-100', ['opacity-50']],
            'display' => [['block', 'flex'], 'flex', ['block']],
            'items' => [['items-start', 'items-center'], 'items-center', ['items-start']],
            'justify' => [['justify-start', 'justify-between'], 'justify-between', ['justify-start']],
            'overflow' => [['overflow-hidden', 'overflow-auto'], 'overflow-auto', ['overflow-hidden']],
            'position' => [['relative', 'absolute'], 'absolute', ['relative']],
            'shadow' => [['shadow-sm', 'shadow-lg'], 'shadow-lg', ['shadow-sm']],
            'space-x' => [['space-x-2', 'space-x-4'], 'space-x-4', ['space-x-2']],
            'space-y' => [['space-y-1', 'space-y-3'], 'space-y-3', ['space-y-1']],
            'min-w' => [['min-w-0', 'min-w-full'], 'min-w-full', ['min-w-0']],
            'max-w' => [['max-w-sm', 'max-w-lg'], 'max-w-lg', ['max-w-sm']],
        ];
    }

    /**
     * @param list<string> $classes
     * @param list<string> $absent
     */
    #[DataProvider('lastWinsProvider')]
    public function testLastConflictingUtilityWins(array $classes, string $expected, array $absent): void
    {
        $merged = (new ClassMerger())->merge($classes);
        $this->assertContains($expected, $merged);
        foreach ($absent as $class) {
            $this->assertNotContains($class, $merged);
        }
    }

    public function testNonConflictingClassesArePreserved(): void
    {
        $merged = (new ClassMerger())->merge(['flex', 'items-center', 'gap-2', 'text-sm', 'sx-btn']);
        foreach (['flex', 'items-center', 'gap-2', 'text-sm', 'sx-btn'] as $class) {
            $this->assertContains($class, $merged);
        }
    }
}
