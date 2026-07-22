<?php

declare(strict_types=1);

namespace Serixa\Theme;

use Serixa\Contracts\ThemeInterface;
use Serixa\Exception\InvalidArgumentException;
use Serixa\Support\ClassMerger;
use Serixa\Token\BorderToken;
use Serixa\Token\BreakpointToken;
use Serixa\Token\ColorToken;
use Serixa\Token\DesignToken;
use Serixa\Token\OpacityToken;
use Serixa\Token\RadiusToken;
use Serixa\Token\ShadowToken;
use Serixa\Token\SpacingToken;
use Serixa\Token\TransitionToken;
use Serixa\Token\TypographyToken;
use Serixa\Token\ZIndexToken;

/**
 * Default Tailwind-backed theme.
 *
 * Widget class maps compose design tokens; tokens alone are swappable per theme.
 */
final class DefaultTheme implements ThemeInterface
{
    /** @var list<int|float|string> */
    private const SPACING_SCALE = [0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 5, 6, 7, 8, 9, 10, 11, 12, 14, 16, 20, 24, 28, 32, 36, 40, 44, 48, 52, 56, 60, 64, 72, 80, 96];

    /** @var array<string, list<string>> */
    private array $classCache = [];

    /** @var array<string, list<string>>|null */
    private ?array $tokenMapCache = null;

    public function __construct(
        private readonly ClassMerger $classMerger = new ClassMerger(),
    ) {
    }

    public function classes(string $component, array $state = []): array
    {
        $key = $component . "\0" . $this->stateCacheKey($state);

        return $this->classCache[$key] ??= $this->resolveClasses($component, $state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function resolveClasses(string $component, array $state): array
    {
        return match ($component) {
            'button' => $this->button($state),
            'text' => $this->text($state),
            'card' => $this->card($state),
            'card.title' => $this->cardTitle($state),
            'container' => $this->container($state),
            'row' => $this->row($state),
            'column' => $this->column($state),
            'center' => $this->center($state),
            'padding' => $this->padding($state),
            'spacer' => $this->spacer($state),
            'expanded' => $this->expanded($state),
            'sizedBox' => $this->sizedBox($state),
            'stack' => $this->stack($state),
            'divider' => $this->divider($state),
            'scaffold' => $this->scaffold($state),
            'scaffold.appBar' => $this->scaffoldAppBar($state),
            'scaffold.sidebar' => $this->scaffoldSidebar($state),
            'scaffold.main' => $this->scaffoldMain($state),
            'scaffold.body' => $this->scaffoldBody($state),
            'scaffold.footer' => $this->scaffoldFooter($state),
            'appBar' => $this->appBar($state),
            'appBar.title' => $this->appBarTitle($state),
            'appBar.actions' => $this->appBarActions($state),
            'sidebar' => $this->sidebar($state),
            'mainContent' => $this->mainContent($state),
            'footer' => $this->footerBar($state),
            'form' => $this->form($state),
            'label' => $this->label($state),
            'input' => $this->input($state),
            'textarea' => $this->textarea($state),
            'checkbox' => $this->checkbox($state),
            'checkbox.wrap' => $this->choiceWrap($state),
            'checkbox.label' => $this->choiceLabel($state),
            'radio' => $this->radio($state),
            'radio.wrap' => $this->choiceWrap($state),
            'radio.label' => $this->choiceLabel($state),
            'switch' => $this->switchControl($state),
            'switch.wrap' => $this->choiceWrap($state),
            'switch.label' => $this->choiceLabel($state),
            'select' => $this->select($state),
            'fieldGroup' => $this->fieldGroup($state),
            'helperText' => $this->helperText($state),
            'errorText' => $this->errorText($state),
            'link' => $this->link($state),
            'table' => $this->table($state),
            'tableHead' => $this->tableHead($state),
            'tableBody' => $this->tableBody($state),
            'tableRow' => $this->tableRow($state),
            'tableCell' => $this->tableCell($state),
            'tableHeaderCell' => $this->tableHeaderCell($state),
            'dataTable' => $this->dataTable($state),
            'dataTable.scroll' => $this->dataTableScroll($state),
            'dataTable.caption' => $this->dataTableCaption($state),
            'searchField' => $this->searchField($state),
            'searchField.input' => $this->searchFieldInput($state),
            'searchField.icon' => $this->searchFieldIcon($state),
            'pagination' => $this->pagination($state),
            'pagination.list' => $this->paginationList($state),
            'pagination.item' => $this->paginationItem($state),
            'pagination.link' => $this->paginationLink($state),
            'pagination.ellipsis' => $this->paginationEllipsis($state),
            'avatar' => $this->avatar($state),
            'avatar.image' => $this->avatarImage($state),
            'avatar.initials' => $this->avatarInitials($state),
            'image' => $this->image($state),
            'logo' => $this->logo($state),
            'logo.image' => $this->logoImage($state),
            'logo.text' => $this->logoText($state),
            'statCard' => $this->statCard($state),
            'statCard.icon' => $this->statCardIcon($state),
            'statCard.title' => $this->statCardTitle($state),
            'statCard.value' => $this->statCardValue($state),
            'statCard.subtitle' => $this->statCardSubtitle($state),
            'statCard.trend' => $this->statCardTrend($state),
            'metric' => $this->metric($state),
            'metric.label' => $this->metricLabel($state),
            'metric.value' => $this->metricValue($state),
            'emptyState' => $this->emptyState($state),
            'emptyState.icon' => $this->emptyStateIcon($state),
            'emptyState.title' => $this->emptyStateTitle($state),
            'emptyState.description' => $this->emptyStateDescription($state),
            'emptyState.action' => $this->emptyStateAction($state),
            'activityList' => $this->activityList($state),
            'activityItem' => $this->activityItem($state),
            'activityItem.icon' => $this->activityItemIcon($state),
            'activityItem.body' => $this->activityItemBody($state),
            'activityItem.title' => $this->activityItemTitle($state),
            'activityItem.description' => $this->activityItemDescription($state),
            'activityItem.timestamp' => $this->activityItemTimestamp($state),
            'pageHeader' => $this->pageHeader($state),
            'pageHeader.text' => $this->pageHeaderText($state),
            'pageHeader.title' => $this->pageHeaderTitle($state),
            'pageHeader.subtitle' => $this->pageHeaderSubtitle($state),
            'pageHeader.actions' => $this->pageHeaderActions($state),
            'toolbar' => $this->toolbar($state),
            'toolbar.leading' => $this->toolbarLeading($state),
            'toolbar.center' => $this->toolbarCenter($state),
            'toolbar.trailing' => $this->toolbarTrailing($state),
            'badge' => $this->badge($state),
            'chip' => $this->chip($state),
            'chip.label' => $this->chipLabel($state),
            'chip.dismiss' => $this->chipDismiss($state),
            'spinner' => $this->spinner($state),
            'spinner.glyph' => $this->spinnerGlyph($state),
            'progress' => $this->progress($state),
            'progress.bar' => $this->progressBar($state),
            'skeleton' => $this->skeleton($state),
            'skeleton.group' => $this->skeletonGroup($state),
            'alert' => $this->alert($state),
            'alert.title' => $this->alertTitle($state),
            'alert.body' => $this->alertBody($state),
            'alert.dismiss' => $this->alertDismiss($state),
            'toast' => $this->toast($state),
            'toast.message' => $this->toastMessage($state),
            'toast.action' => $this->toastAction($state),
            'modal' => $this->modal($state),
            'modal.backdrop' => $this->modalBackdrop($state),
            'modal.dialog' => $this->modalDialog($state),
            'modal.title' => $this->modalTitle($state),
            'modal.body' => $this->modalBody($state),
            'modal.footer' => $this->modalFooter($state),
            'modal.close' => $this->modalClose($state),
            'drawer' => $this->drawer($state),
            'drawer.backdrop' => $this->drawerBackdrop($state),
            'drawer.panel' => $this->drawerPanel($state),
            'drawer.title' => $this->drawerTitle($state),
            'drawer.body' => $this->drawerBody($state),
            'drawer.close' => $this->drawerClose($state),
            'popover' => $this->popover($state),
            'popover.trigger' => $this->popoverTrigger($state),
            'popover.panel' => $this->popoverPanel($state),
            'tooltip' => $this->tooltip($state),
            'tooltip.trigger' => $this->tooltipTrigger($state),
            'tooltip.content' => $this->tooltipContent($state),
            'dropdown' => $this->dropdown($state),
            'dropdown.trigger' => $this->dropdownTrigger($state),
            'dropdown.panel' => $this->dropdownPanel($state),
            'dropdownMenu' => $this->dropdownMenu($state),
            'dropdownItem' => $this->dropdownItem($state),
            'accordion' => $this->accordion($state),
            'accordionItem' => $this->accordionItem($state),
            'accordion.heading' => $this->accordionHeading($state),
            'accordion.button' => $this->accordionButton($state),
            'accordion.panel' => $this->accordionPanel($state),
            'tabs' => $this->tabs($state),
            'tabs.list' => $this->tabsList($state),
            'tabs.tab' => $this->tabsTab($state),
            'tabs.panel' => $this->tabsPanel($state),
            'icon' => $this->icon($state),
            default => throw new InvalidArgumentException(sprintf(
                'Unknown theme component "%s". Tip: use DefaultTheme::classes() keys such as "button", "card", or "modal".',
                $component,
            )),
        };
    }

    public function token(DesignToken|string $token): array
    {
        $name = $token instanceof DesignToken ? $token->name() : $token;
        $map = $this->tokenMap();

        if (! isset($map[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Unknown design token "%s". Tip: use ColorToken::*, SpacingToken::*, or TypographyToken::* factories.',
                $name,
            ));
        }

        return $map[$name];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     */
    private function stateCacheKey(array $state): string
    {
        if ($state === []) {
            return '';
        }

        ksort($state);

        return (string) json_encode($state, JSON_THROW_ON_ERROR);
    }

    public function buttonPrimary(): array
    {
        return $this->classes('button', ['variant' => 'primary']);
    }

    public function buttonSecondary(): array
    {
        return $this->classes('button', ['variant' => 'secondary']);
    }

    public function buttonDanger(): array
    {
        return $this->classes('button', ['variant' => 'danger']);
    }

    public function buttonGhost(): array
    {
        return $this->classes('button', ['variant' => 'ghost']);
    }

    /**
     * @return array<string, list<string>>
     */
    private function tokenMap(): array
    {
        return $this->tokenMapCache ??= [
            ColorToken::primary()->name() => ['bg-sky-600', 'hover:bg-sky-700'],
            ColorToken::primaryHover()->name() => ['bg-sky-700', 'hover:bg-sky-800'],
            ColorToken::secondary()->name() => ['bg-slate-100', 'hover:bg-slate-200'],
            ColorToken::secondaryHover()->name() => ['bg-slate-200', 'hover:bg-slate-300'],
            ColorToken::danger()->name() => ['bg-red-600', 'hover:bg-red-700'],
            ColorToken::dangerHover()->name() => ['bg-red-700', 'hover:bg-red-800'],
            ColorToken::onPrimary()->name() => ['text-white'],
            ColorToken::surface()->name() => ['bg-white', 'dark:bg-slate-900'],
            ColorToken::background()->name() => ['bg-slate-50', 'dark:bg-slate-950'],
            ColorToken::textPrimary()->name() => ['text-slate-900', 'dark:text-slate-100'],
            ColorToken::textMuted()->name() => ['text-slate-500', 'dark:text-slate-400'],
            SpacingToken::small()->name() => ['p-4'],
            SpacingToken::medium()->name() => ['p-6'],
            SpacingToken::large()->name() => ['p-8'],
            RadiusToken::small()->name() => ['rounded'],
            RadiusToken::medium()->name() => ['rounded-md'],
            RadiusToken::large()->name() => ['rounded-lg'],
            RadiusToken::full()->name() => ['rounded-full'],
            ShadowToken::small()->name() => ['shadow-sm'],
            ShadowToken::medium()->name() => ['shadow'],
            ShadowToken::large()->name() => ['shadow-lg'],
            TypographyToken::body()->name() => ['text-base', 'font-normal'],
            TypographyToken::heading()->name() => ['text-lg', 'font-semibold'],
            TypographyToken::label()->name() => ['text-sm', 'font-medium'],
            TypographyToken::muted()->name() => ['text-slate-500', 'dark:text-slate-400'],
            BorderToken::subtle()->name() => ['border-slate-200', 'dark:border-slate-700'],
            BorderToken::strong()->name() => ['border-slate-400', 'dark:border-slate-500'],
            BorderToken::focus()->name() => ['outline-sky-600', 'dark:outline-sky-400'],
            OpacityToken::disabled()->name() => ['opacity-50'],
            OpacityToken::muted()->name() => ['opacity-70'],
            OpacityToken::overlay()->name() => ['opacity-40'],
            ZIndexToken::base()->name() => ['z-0'],
            ZIndexToken::dropdown()->name() => ['z-40'],
            ZIndexToken::sticky()->name() => ['z-50'],
            ZIndexToken::modal()->name() => ['z-[60]'],
            ZIndexToken::toast()->name() => ['z-[70]'],
            BreakpointToken::sm()->name() => ['sm'],
            BreakpointToken::md()->name() => ['md'],
            BreakpointToken::lg()->name() => ['lg'],
            BreakpointToken::xl()->name() => ['xl'],
            TransitionToken::fast()->name() => ['duration-100'],
            TransitionToken::normal()->name() => ['duration-150'],
            TransitionToken::slow()->name() => ['duration-300'],
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function button(array $state): array
    {
        $variant = (string) ($state['variant'] ?? 'primary');
        $size = (string) ($state['size'] ?? 'md');
        $disabled = (bool) ($state['disabled'] ?? false);
        $rounded = (bool) ($state['rounded'] ?? false);

        $base = [
            'sx-btn',
            'inline-flex',
            'items-center',
            'justify-center',
            'font-medium',
            'transition-colors',
            'focus-visible:outline',
            'focus-visible:outline-2',
            'focus-visible:outline-offset-2',
            'focus-visible:outline-sky-600',
        ];

        $sizes = [
            'sm' => ['sx-btn-sm', 'text-sm', 'px-3', 'py-1.5'],
            'md' => ['sx-btn-md', 'text-sm', 'px-4', 'py-2'],
            'lg' => ['sx-btn-lg', 'text-base', 'px-5', 'py-2.5'],
        ];

        $variants = [
            'primary' => ['sx-btn-primary', 'text-white'],
            'secondary' => ['sx-btn-secondary'],
            'danger' => ['sx-btn-danger', 'text-white'],
            'ghost' => ['sx-btn-ghost', 'bg-transparent', 'text-slate-700', 'hover:bg-slate-100'],
        ];

        if (! isset($sizes[$size], $variants[$variant])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid button theme state: variant="%s" size="%s". Allowed variants: %s. Allowed sizes: %s.',
                $variant,
                $size,
                implode(', ', array_keys($variants)),
                implode(', ', array_keys($sizes)),
            ));
        }

        return $this->classMerger->merge(
            $base,
            $this->token($rounded ? RadiusToken::full() : RadiusToken::medium()),
            $rounded ? ['sx-btn-rounded'] : [],
            $sizes[$size],
            $variants[$variant],
            $disabled ? ['sx-btn-disabled', 'opacity-50', 'cursor-not-allowed', 'pointer-events-none'] : [],
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function text(array $state): array
    {
        $size = (string) ($state['size'] ?? 'md');
        $weight = (string) ($state['weight'] ?? 'normal');
        $tone = (string) ($state['tone'] ?? 'default');

        $sizes = [
            'xs' => 'text-xs',
            'sm' => 'text-sm',
            'md' => 'text-base',
            'lg' => 'text-lg',
            'xl' => 'text-xl',
            '2xl' => 'text-2xl',
        ];

        $weights = [
            'normal' => 'font-normal',
            'medium' => 'font-medium',
            'semibold' => 'font-semibold',
            'bold' => 'font-bold',
        ];

        $tones = [
            'default' => $this->token(ColorToken::textPrimary())[0],
            'muted' => $this->token(ColorToken::textMuted())[0],
            'primary' => 'text-sky-700',
            'danger' => 'text-red-700',
        ];

        if (! isset($sizes[$size], $weights[$weight], $tones[$tone])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid text theme state: size="%s" weight="%s" tone="%s".',
                $size,
                $weight,
                $tone,
            ));
        }

        return [$sizes[$size], $weights[$weight], $tones[$tone]];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function card(array $state): array
    {
        $padding = (string) ($state['padding'] ?? 'md');

        $paddings = [
            'sm' => $this->token(SpacingToken::small()),
            'md' => $this->token(SpacingToken::medium()),
            'lg' => $this->token(SpacingToken::large()),
        ];

        if (! isset($paddings[$padding])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid card theme state: padding="%s". Allowed: %s.',
                $padding,
                implode(', ', array_keys($paddings)),
            ));
        }

        return $this->classMerger->merge(
            $this->token(RadiusToken::large()),
            ['border', 'border-slate-200', 'dark:border-slate-700'],
            $this->token(ColorToken::surface()),
            $this->token(ShadowToken::small()),
            $paddings[$padding],
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function cardTitle(array $state): array
    {
        return $this->classMerger->merge(
            ['mb-4'],
            $this->token(TypographyToken::heading()),
            $this->token(ColorToken::textPrimary()),
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function container(array $state): array
    {
        $maxWidth = (string) ($state['maxWidth'] ?? 'lg');
        $padding = (string) ($state['padding'] ?? 'md');

        $maxWidths = [
            'sm' => 'max-w-screen-sm',
            'md' => 'max-w-screen-md',
            'lg' => 'max-w-screen-lg',
            'xl' => 'max-w-screen-xl',
            'full' => 'max-w-full',
        ];

        $paddings = [
            'none' => [],
            'sm' => ['px-4', 'py-4'],
            'md' => ['px-6', 'py-6'],
            'lg' => ['px-8', 'py-8'],
        ];

        if (! isset($maxWidths[$maxWidth], $paddings[$padding])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid container theme state: maxWidth="%s" padding="%s".',
                $maxWidth,
                $padding,
            ));
        }

        return $this->classMerger->merge(
            ['mx-auto', $maxWidths[$maxWidth]],
            // Default fill when state omits the key (theme-only callers).
            (! array_key_exists('fill', $state) || ! empty($state['fill'])) ? ['w-full'] : [],
            $paddings[$padding],
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function row(array $state): array
    {
        $justify = (string) ($state['justify'] ?? 'start');
        $align = (string) ($state['align'] ?? 'stretch');
        $gap = $state['gap'] ?? null;

        $justifies = [
            'start' => 'justify-start',
            'center' => 'justify-center',
            'end' => 'justify-end',
            'between' => 'justify-between',
            'around' => 'justify-around',
        ];

        $aligns = [
            'start' => 'items-start',
            'center' => 'items-center',
            'end' => 'items-end',
            'stretch' => 'items-stretch',
        ];

        if (! isset($justifies[$justify], $aligns[$align])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid row theme state: justify="%s" align="%s".',
                $justify,
                $align,
            ));
        }

        $classes = ['flex', 'flex-row', $justifies[$justify], $aligns[$align]];

        if (! empty($state['fill'])) {
            $classes[] = 'w-full';
        }

        if ($gap !== null) {
            $classes[] = $this->spacingClass('gap', $gap);
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function column(array $state): array
    {
        $align = (string) ($state['align'] ?? 'stretch');
        $gap = $state['gap'] ?? null;

        $aligns = [
            'start' => 'items-start',
            'center' => 'items-center',
            'end' => 'items-end',
            'stretch' => 'items-stretch',
        ];

        if (! isset($aligns[$align])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid column theme state: align="%s". Allowed: %s.',
                $align,
                implode(', ', array_keys($aligns)),
            ));
        }

        $classes = ['flex', 'flex-col', $aligns[$align]];

        if (! empty($state['fill'])) {
            $classes[] = 'w-full';
        }

        if ($gap !== null) {
            $classes[] = $this->spacingClass('gap', $gap);
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function center(array $state): array
    {
        $classes = [
            'flex',
            'items-center',
            'justify-center',
        ];

        if (! empty($state['fill'])) {
            $classes[] = 'w-full';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function padding(array $state): array
    {
        $classes = [];

        foreach (['all' => 'p', 'x' => 'px', 'y' => 'py', 'top' => 'pt', 'bottom' => 'pb', 'left' => 'pl', 'right' => 'pr'] as $key => $prefix) {
            if (array_key_exists($key, $state) && $state[$key] !== null) {
                $classes[] = $this->spacingClass($prefix, $state[$key]);
            }
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function spacer(array $state): array
    {
        return ['flex-1'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function expanded(array $state): array
    {
        return ['flex-1', 'min-w-0', 'min-h-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function sizedBox(array $state): array
    {
        $classes = [];

        if (array_key_exists('width', $state) && $state['width'] !== null) {
            $classes[] = $this->spacingClass('w', $state['width']);
        }

        if (array_key_exists('height', $state) && $state['height'] !== null) {
            $classes[] = $this->spacingClass('h', $state['height']);
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function stack(array $state): array
    {
        $classes = ['relative'];

        if (! empty($state['fill'])) {
            $classes[] = 'w-full';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function divider(array $state): array
    {
        $orientation = (string) ($state['orientation'] ?? 'horizontal');
        $spacing = (string) ($state['spacing'] ?? 'md');

        $spacingMap = [
            'none' => ['my-0', 'mx-0'],
            'sm' => ['my-2', 'mx-2'],
            'md' => ['my-4', 'mx-4'],
            'lg' => ['my-6', 'mx-6'],
        ];

        if (! isset($spacingMap[$spacing])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid divider spacing "%s". Allowed: %s.',
                $spacing,
                implode(', ', array_keys($spacingMap)),
            ));
        }

        if ($orientation === 'vertical') {
            return [
                'border-0',
                'border-l',
                'border-slate-200',
                'dark:border-slate-700',
                'h-auto',
                'self-stretch',
                'w-px',
                $spacingMap[$spacing][1],
            ];
        }

        return [
            'border-0',
            'border-t',
            'border-slate-200',
            'dark:border-slate-700',
            'w-full',
            $spacingMap[$spacing][0],
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function appBar(array $state): array
    {
        return [
            'flex',
            'w-full',
            'items-center',
            'justify-between',
            'gap-4',
            'px-4',
            'py-3',
            'bg-slate-900',
            'text-white',
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function appBarTitle(array $state): array
    {
        return ['text-lg', 'font-semibold', 'tracking-tight'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function appBarActions(array $state): array
    {
        return ['flex', 'items-center', 'gap-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function sidebar(array $state): array
    {
        return [
            'flex',
            'flex-col',
            'gap-2',
            'p-4',
            'w-56',
            'min-h-full',
            'bg-slate-50',
            'dark:bg-slate-900',
            'border-r',
            'border-slate-200',
            'dark:border-slate-700',
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function mainContent(array $state): array
    {
        return ['flex', 'flex-col', 'gap-4', 'p-6', 'w-full', 'min-w-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function footerBar(array $state): array
    {
        return [
            'w-full',
            'px-6',
            'py-4',
            'text-sm',
            'text-slate-500',
            'border-t',
            'border-slate-200',
            'dark:border-slate-700',
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function form(array $state): array
    {
        return ['flex', 'flex-col', 'gap-4', 'w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function label(array $state): array
    {
        return ['block', 'text-sm', 'font-medium', 'text-slate-700', 'dark:text-slate-200', 'mb-1'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function input(array $state): array
    {
        return $this->controlField($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function textarea(array $state): array
    {
        return array_merge($this->controlField($state), ['min-h-[5rem]']);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function select(array $state): array
    {
        return $this->controlField($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function controlField(array $state): array
    {
        $classes = [
            'block',
            'w-full',
            'rounded-md',
            'border',
            'border-slate-300',
            'dark:border-slate-600',
            'bg-white',
            'dark:bg-slate-900',
            'px-3',
            'py-2',
            'text-sm',
            'text-slate-900',
            'dark:text-slate-100',
            'shadow-sm',
            'focus:outline-none',
            'focus:ring-2',
            'focus:ring-sky-500',
            'focus:border-sky-500',
        ];

        if (! empty($state['invalid'])) {
            $classes[] = 'border-rose-500';
            $classes[] = 'focus:ring-rose-500';
            $classes[] = 'focus:border-rose-500';
        }

        if (! empty($state['disabled'])) {
            $classes[] = 'opacity-60';
            $classes[] = 'cursor-not-allowed';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function checkbox(array $state): array
    {
        return $this->choiceControl($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function radio(array $state): array
    {
        return $this->choiceControl($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function switchControl(array $state): array
    {
        return array_merge($this->choiceControl($state), ['accent-sky-600']);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function choiceControl(array $state): array
    {
        $classes = ['h-4', 'w-4', 'rounded', 'border-slate-300', 'text-sky-600', 'focus:ring-sky-500'];

        if (! empty($state['disabled'])) {
            $classes[] = 'opacity-60';
            $classes[] = 'cursor-not-allowed';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function choiceWrap(array $state): array
    {
        return ['flex', 'items-center', 'gap-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function choiceLabel(array $state): array
    {
        return ['text-sm', 'text-slate-700', 'dark:text-slate-200'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function fieldGroup(array $state): array
    {
        return ['flex', 'flex-col', 'gap-1', 'w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function helperText(array $state): array
    {
        return ['text-xs', 'text-slate-500', 'dark:text-slate-400'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function errorText(array $state): array
    {
        return ['text-xs', 'font-medium', 'text-rose-600', 'dark:text-rose-400'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function link(array $state): array
    {
        $classes = [
            'text-sm',
            'font-medium',
            'text-sky-600',
            'hover:text-sky-700',
            'underline-offset-2',
            'hover:underline',
        ];

        if (! empty($state['active'])) {
            $classes[] = 'text-sky-800';
            $classes[] = 'font-semibold';
            $classes[] = 'underline';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function scaffold(array $state): array
    {
        return ['flex', 'flex-col', 'min-h-full', 'w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function scaffoldAppBar(array $state): array
    {
        return ['w-full', 'shrink-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function scaffoldSidebar(array $state): array
    {
        return ['shrink-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function scaffoldMain(array $state): array
    {
        return ['flex', 'flex-1', 'w-full', 'min-h-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function scaffoldBody(array $state): array
    {
        return ['flex-1', 'min-w-0', 'min-h-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function scaffoldFooter(array $state): array
    {
        return ['w-full', 'shrink-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function table(array $state): array
    {
        $classes = ['w-full', 'text-left', 'text-sm', 'border-collapse'];

        if (! empty($state['compact'])) {
            $classes[] = 'text-xs';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tableHead(array $state): array
    {
        return ['bg-slate-50', 'dark:bg-slate-800'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tableBody(array $state): array
    {
        return ['divide-y', 'divide-slate-200', 'dark:divide-slate-700'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tableRow(array $state): array
    {
        return [];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tableCell(array $state): array
    {
        return ['px-3', 'py-2', 'align-middle', 'text-slate-700', 'dark:text-slate-200'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tableHeaderCell(array $state): array
    {
        return ['px-3', 'py-2', 'font-semibold', 'text-slate-900', 'dark:text-slate-100'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dataTable(array $state): array
    {
        $classes = [
            'w-full',
            'rounded-lg',
            'border',
            'border-slate-200',
            'dark:border-slate-700',
            'bg-white',
            'dark:bg-slate-900',
            'overflow-hidden',
        ];

        if (! empty($state['striped'])) {
            $classes[] = '[&_tbody_tr:nth-child(odd)]:bg-slate-50';
            $classes[] = 'dark:[&_tbody_tr:nth-child(odd)]:bg-slate-800/50';
        }

        if (! empty($state['hover'])) {
            $classes[] = '[&_tbody_tr:hover]:bg-slate-100';
            $classes[] = 'dark:[&_tbody_tr:hover]:bg-slate-800';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dataTableScroll(array $state): array
    {
        return ['w-full', 'overflow-x-auto'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dataTableCaption(array $state): array
    {
        return ['caption-top', 'px-3', 'py-2', 'text-left', 'text-sm', 'text-slate-500'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function searchField(array $state): array
    {
        return ['relative', 'flex', 'items-center', 'w-full', 'max-w-md'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function searchFieldInput(array $state): array
    {
        return array_merge($this->controlField($state), ['pr-3']);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function searchFieldIcon(array $state): array
    {
        return ['absolute', 'left-3', 'text-slate-400', 'pointer-events-none'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function pagination(array $state): array
    {
        return ['w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function paginationList(array $state): array
    {
        return ['flex', 'flex-wrap', 'items-center', 'gap-1', 'list-none', 'p-0', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function paginationItem(array $state): array
    {
        return [];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function paginationLink(array $state): array
    {
        $classes = [
            'inline-flex',
            'items-center',
            'justify-center',
            'min-w-9',
            'h-9',
            'px-3',
            'rounded-md',
            'text-sm',
            'border',
            'border-slate-200',
            'dark:border-slate-700',
        ];

        if (! empty($state['current'])) {
            $classes[] = 'bg-sky-600';
            $classes[] = 'text-white';
            $classes[] = 'border-sky-600';
        }

        if (! empty($state['disabled'])) {
            $classes[] = 'opacity-50';
            $classes[] = 'cursor-not-allowed';
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function paginationEllipsis(array $state): array
    {
        return ['px-2', 'text-slate-400'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function avatar(array $state): array
    {
        $sizes = ['sm' => 'h-8 w-8 text-xs', 'md' => 'h-10 w-10 text-sm', 'lg' => 'h-14 w-14 text-base', 'xl' => 'h-20 w-20 text-lg'];
        $size = (string) ($state['size'] ?? 'md');
        $shape = (string) ($state['shape'] ?? 'circle');

        return [
            'inline-flex',
            'items-center',
            'justify-center',
            'overflow-hidden',
            'bg-slate-200',
            'dark:bg-slate-700',
            'text-slate-700',
            'dark:text-slate-100',
            'font-semibold',
            $sizes[$size] ?? $sizes['md'],
            $shape === 'square' ? 'rounded-md' : 'rounded-full',
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function avatarImage(array $state): array
    {
        return ['h-full', 'w-full', 'object-cover'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function avatarInitials(array $state): array
    {
        return ['leading-none'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function image(array $state): array
    {
        return ['max-w-full', 'h-auto'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function logo(array $state): array
    {
        return ['inline-flex', 'items-center', 'gap-2', 'font-bold', 'text-slate-900', 'dark:text-slate-100', 'no-underline'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function logoImage(array $state): array
    {
        return ['h-8', 'w-8', 'object-contain'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function logoText(array $state): array
    {
        return ['text-lg', 'tracking-tight'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function statCard(array $state): array
    {
        $colors = [
            'default' => 'border-slate-200 dark:border-slate-700',
            'primary' => 'border-sky-300',
            'success' => 'border-emerald-300',
            'warning' => 'border-amber-300',
            'danger' => 'border-rose-300',
        ];
        $color = (string) ($state['color'] ?? 'default');

        return [
            'flex',
            'flex-col',
            'gap-1',
            'rounded-lg',
            'border',
            'bg-white',
            'dark:bg-slate-900',
            'p-4',
            'shadow-sm',
            'min-w-[10rem]',
            'flex-1',
            $colors[$color] ?? $colors['default'],
        ];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function statCardIcon(array $state): array
    {
        return ['text-slate-400', 'mb-1'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function statCardTitle(array $state): array
    {
        return ['text-sm', 'text-slate-500'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function statCardValue(array $state): array
    {
        return ['text-2xl', 'font-bold', 'text-slate-900', 'dark:text-slate-100'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function statCardSubtitle(array $state): array
    {
        return ['text-xs', 'text-slate-500'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function statCardTrend(array $state): array
    {
        return ['text-xs', 'font-medium', 'text-emerald-600'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function metric(array $state): array
    {
        return ['inline-flex', 'flex-col', 'gap-0.5', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function metricLabel(array $state): array
    {
        return ['text-xs', 'text-slate-500', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function metricValue(array $state): array
    {
        return ['text-sm', 'font-semibold', 'text-slate-900', 'dark:text-slate-100', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function emptyState(array $state): array
    {
        return ['flex', 'flex-col', 'items-center', 'justify-center', 'gap-2', 'text-center', 'px-6', 'py-10'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function emptyStateIcon(array $state): array
    {
        return ['text-slate-400', 'text-3xl'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function emptyStateTitle(array $state): array
    {
        return ['text-lg', 'font-semibold', 'text-slate-900', 'dark:text-slate-100', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function emptyStateDescription(array $state): array
    {
        return ['text-sm', 'text-slate-500', 'm-0', 'max-w-sm'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function emptyStateAction(array $state): array
    {
        return ['mt-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityList(array $state): array
    {
        return ['flex', 'flex-col', 'gap-3', 'list-none', 'p-0', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityItem(array $state): array
    {
        return ['flex', 'gap-3', 'items-start'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityItemIcon(array $state): array
    {
        return ['mt-0.5', 'text-slate-400'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityItemBody(array $state): array
    {
        return ['flex', 'flex-col', 'gap-0.5', 'min-w-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityItemTitle(array $state): array
    {
        return ['text-sm', 'font-medium', 'text-slate-900', 'dark:text-slate-100'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityItemDescription(array $state): array
    {
        return ['text-sm', 'text-slate-500'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function activityItemTimestamp(array $state): array
    {
        return ['text-xs', 'text-slate-400'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function pageHeader(array $state): array
    {
        return ['flex', 'flex-wrap', 'items-start', 'justify-between', 'gap-4', 'w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function pageHeaderText(array $state): array
    {
        return ['flex', 'flex-col', 'gap-1', 'min-w-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function pageHeaderTitle(array $state): array
    {
        return ['text-2xl', 'font-bold', 'text-slate-900', 'dark:text-slate-100', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function pageHeaderSubtitle(array $state): array
    {
        return ['text-sm', 'text-slate-500', 'm-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function pageHeaderActions(array $state): array
    {
        return ['flex', 'items-center', 'gap-2', 'flex-shrink-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toolbar(array $state): array
    {
        return ['flex', 'flex-wrap', 'items-center', 'justify-between', 'gap-3', 'w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toolbarLeading(array $state): array
    {
        return ['flex', 'items-center', 'gap-2', 'flex-1', 'min-w-[12rem]'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toolbarCenter(array $state): array
    {
        return ['flex', 'items-center', 'gap-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toolbarTrailing(array $state): array
    {
        return ['flex', 'items-center', 'gap-2'];
    }

    /**
     * Icon widget (serixa/icons) — size + semantic text color for SVG currentColor.
     *
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function icon(array $state): array
    {
        $size = (string) ($state['size'] ?? 'md');
        $color = (string) ($state['color'] ?? 'inherit');

        $sizes = [
            'xs' => ['w-3', 'h-3'],
            'sm' => ['w-4', 'h-4'],
            'md' => ['w-5', 'h-5'],
            'lg' => ['w-6', 'h-6'],
            'xl' => ['w-8', 'h-8'],
            '2xl' => ['w-10', 'h-10'],
        ];

        $colors = [
            'primary' => ['text-sky-600'],
            'secondary' => ['text-slate-600'],
            'success' => ['text-emerald-600'],
            'danger' => ['text-red-600'],
            'warning' => ['text-amber-600'],
            'muted' => $this->token(ColorToken::textMuted()),
            'inherit' => ['text-inherit'],
        ];

        if (! isset($sizes[$size])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid icon theme size "%s". Allowed: %s.',
                $size,
                implode(', ', array_keys($sizes)),
            ));
        }

        if (! isset($colors[$color])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid icon theme color "%s". Allowed: %s.',
                $color,
                implode(', ', array_keys($colors)),
            ));
        }

        return $this->classMerger->merge(
            ['inline-block', 'shrink-0', 'align-middle'],
            $sizes[$size],
            $colors[$color],
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function badge(array $state): array
    {
        $color = (string) ($state['color'] ?? 'neutral');
        $tones = [
            'neutral' => ['bg-slate-100', 'text-slate-700'],
            'primary' => ['bg-teal-100', 'text-teal-800'],
            'success' => ['bg-emerald-100', 'text-emerald-800'],
            'warning' => ['bg-amber-100', 'text-amber-900'],
            'danger' => ['bg-rose-100', 'text-rose-800'],
        ];

        return array_merge(
            ['inline-flex', 'items-center', 'rounded-full', 'px-2.5', 'py-0.5', 'text-xs', 'font-semibold'],
            $tones[$color] ?? $tones['neutral'],
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function chip(array $state): array
    {
        return array_merge($this->badge($state), ['gap-1', 'pr-1']);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function chipLabel(array $state): array
    {
        return ['px-1'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function chipDismiss(array $state): array
    {
        return ['ml-1', 'rounded-full', 'px-1', 'text-sm', 'hover:bg-black/10'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function spinner(array $state): array
    {
        return ['inline-flex', 'items-center', 'justify-center', 'text-teal-700'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function spinnerGlyph(array $state): array
    {
        $size = (string) ($state['size'] ?? 'md');
        $map = ['sm' => 'h-4 w-4', 'md' => 'h-6 w-6', 'lg' => 'h-8 w-8'];

        return array_merge(
            ['inline-block', 'animate-spin', 'rounded-full', 'border-2', 'border-teal-200', 'border-t-teal-700'],
            explode(' ', $map[$size] ?? $map['md']),
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function progress(array $state): array
    {
        $size = (string) ($state['size'] ?? 'md');
        $h = ['sm' => 'h-1.5', 'md' => 'h-2.5', 'lg' => 'h-4'];

        return ['w-full', 'overflow-hidden', 'rounded-full', 'bg-slate-200', $h[$size] ?? 'h-2.5'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function progressBar(array $state): array
    {
        $base = ['h-full', 'rounded-full', 'bg-teal-600'];
        if (! empty($state['indeterminate'])) {
            return array_merge($base, ['w-1/3', 'animate-pulse']);
        }

        return $base;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function skeleton(array $state): array
    {
        $variant = (string) ($state['variant'] ?? 'text');
        $base = ['animate-pulse', 'rounded', 'bg-slate-200'];

        return match ($variant) {
            'avatar' => array_merge($base, ['h-12', 'w-12', 'rounded-full']),
            'card' => array_merge($base, ['h-24', 'w-full']),
            'rect' => array_merge($base, ['h-16', 'w-full']),
            default => array_merge($base, ['h-3', 'w-full']),
        };
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function skeletonGroup(array $state): array
    {
        return ['flex', 'flex-col', 'gap-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function alert(array $state): array
    {
        $tone = (string) ($state['tone'] ?? 'info');
        $tones = [
            'info' => ['bg-sky-50', 'text-sky-900', 'border-sky-200'],
            'success' => ['bg-emerald-50', 'text-emerald-900', 'border-emerald-200'],
            'warning' => ['bg-amber-50', 'text-amber-950', 'border-amber-200'],
            'danger' => ['bg-rose-50', 'text-rose-900', 'border-rose-200'],
        ];

        return array_merge(
            ['relative', 'flex', 'gap-3', 'rounded-lg', 'border', 'p-4', 'text-sm'],
            $tones[$tone] ?? $tones['info'],
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function alertTitle(array $state): array
    {
        return ['block', 'font-semibold'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function alertBody(array $state): array
    {
        return ['mt-1'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function alertDismiss(array $state): array
    {
        return ['absolute', 'right-2', 'top-2', 'rounded', 'px-1', 'hover:bg-black/5'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toast(array $state): array
    {
        $tone = (string) ($state['tone'] ?? 'info');

        return array_merge(
            ['fixed', 'bottom-4', 'right-4', 'z-50', 'max-w-sm', 'rounded-lg', 'border', 'bg-white', 'p-4', 'shadow-lg'],
            $this->alert(['tone' => $tone]),
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toastMessage(array $state): array
    {
        return ['text-sm', 'font-medium'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function toastAction(array $state): array
    {
        return ['mt-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modal(array $state): array
    {
        return ['fixed', 'inset-0', 'z-50', 'flex', 'items-center', 'justify-center', 'p-4'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modalBackdrop(array $state): array
    {
        return ['absolute', 'inset-0', 'bg-slate-900/50'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modalDialog(array $state): array
    {
        return ['relative', 'z-10', 'w-full', 'max-w-lg', 'rounded-xl', 'bg-white', 'p-6', 'shadow-xl'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modalTitle(array $state): array
    {
        return ['mb-2', 'pr-8', 'text-lg', 'font-semibold', 'text-slate-900'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modalBody(array $state): array
    {
        return ['text-sm', 'text-slate-600'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modalFooter(array $state): array
    {
        return ['mt-6', 'flex', 'justify-end', 'gap-2'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function modalClose(array $state): array
    {
        return ['absolute', 'right-3', 'top-3', 'rounded', 'px-2', 'text-lg', 'text-slate-500', 'hover:bg-slate-100'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function drawer(array $state): array
    {
        return ['fixed', 'inset-0', 'z-50'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function drawerBackdrop(array $state): array
    {
        return ['absolute', 'inset-0', 'bg-slate-900/40'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function drawerPanel(array $state): array
    {
        $side = (string) ($state['side'] ?? 'right');
        $pos = $side === 'left' ? ['left-0'] : ['right-0'];

        return array_merge(
            ['absolute', 'top-0', 'h-full', 'w-full', 'max-w-sm', 'bg-white', 'p-6', 'shadow-xl'],
            $pos,
        );
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function drawerTitle(array $state): array
    {
        return $this->modalTitle($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function drawerBody(array $state): array
    {
        return $this->modalBody($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function drawerClose(array $state): array
    {
        return $this->modalClose($state);
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function popover(array $state): array
    {
        return ['relative', 'inline-block'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function popoverTrigger(array $state): array
    {
        return ['inline-flex'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function popoverPanel(array $state): array
    {
        return ['absolute', 'z-20', 'mt-2', 'min-w-[12rem]', 'rounded-lg', 'border', 'border-slate-200', 'bg-white', 'p-3', 'shadow-lg'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tooltip(array $state): array
    {
        return ['relative', 'inline-flex'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tooltipTrigger(array $state): array
    {
        return ['inline-flex'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tooltipContent(array $state): array
    {
        return ['absolute', 'bottom-full', 'left-1/2', 'z-30', 'mb-2', '-translate-x-1/2', 'whitespace-nowrap', 'rounded', 'bg-slate-900', 'px-2', 'py-1', 'text-xs', 'text-white'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dropdown(array $state): array
    {
        return ['relative', 'inline-block'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dropdownTrigger(array $state): array
    {
        return ['inline-flex'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dropdownPanel(array $state): array
    {
        return ['absolute', 'right-0', 'z-20', 'mt-2', 'min-w-[10rem]', 'rounded-lg', 'border', 'border-slate-200', 'bg-white', 'py-1', 'shadow-lg'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dropdownMenu(array $state): array
    {
        return ['flex', 'flex-col'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function dropdownItem(array $state): array
    {
        $classes = ['block', 'w-full', 'px-3', 'py-2', 'text-left', 'text-sm', 'text-slate-700', 'hover:bg-slate-50'];
        if (! empty($state['danger'])) {
            $classes[] = 'text-rose-700';
        }
        if (! empty($state['disabled'])) {
            $classes = array_merge($classes, ['opacity-50', 'pointer-events-none']);
        }

        return $classes;
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function accordion(array $state): array
    {
        return ['divide-y', 'divide-slate-200', 'rounded-lg', 'border', 'border-slate-200', 'bg-white'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function accordionItem(array $state): array
    {
        return ['p-0'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function accordionHeading(array $state): array
    {
        return ['m-0', 'text-base', 'font-medium'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function accordionButton(array $state): array
    {
        return ['flex', 'w-full', 'items-center', 'justify-between', 'px-4', 'py-3', 'text-left', 'hover:bg-slate-50'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function accordionPanel(array $state): array
    {
        return ['px-4', 'pb-4', 'text-sm', 'text-slate-600'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tabs(array $state): array
    {
        return ['w-full'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tabsList(array $state): array
    {
        return ['mb-4', 'flex', 'gap-1', 'border-b', 'border-slate-200'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tabsTab(array $state): array
    {
        $active = ! empty($state['active']);

        return $active
            ? ['-mb-px', 'border-b-2', 'border-teal-600', 'px-3', 'py-2', 'text-sm', 'font-semibold', 'text-teal-800']
            : ['-mb-px', 'border-b-2', 'border-transparent', 'px-3', 'py-2', 'text-sm', 'text-slate-600', 'hover:text-slate-900'];
    }

    /**
     * @param array<string, scalar|bool|null> $state
     * @return list<string>
     */
    private function tabsPanel(array $state): array
    {
        return ['text-sm', 'text-slate-700'];
    }

    private function spacingClass(string $prefix, mixed $value): string
    {
        if (is_string($value) && in_array($value, ['sm', 'md', 'lg'], true)) {
            $map = ['sm' => 2, 'md' => 4, 'lg' => 6];

            return $prefix . '-' . $map[$value];
        }

        if (! is_int($value) && ! is_float($value) && ! (is_string($value) && is_numeric($value))) {
            throw new InvalidArgumentException(sprintf('Invalid spacing value for "%s".', $prefix));
        }

        $normalized = is_string($value) ? (str_contains($value, '.') ? (float) $value : (int) $value) : $value;

        if (! in_array($normalized, self::SPACING_SCALE, true)) {
            throw new InvalidArgumentException(sprintf(
                'Spacing value "%s" is not on the Tailwind scale.',
                (string) $value,
            ));
        }

        $token = is_float($normalized) ? (string) $normalized : (string) $normalized;

        return $prefix . '-' . $token;
    }
}
