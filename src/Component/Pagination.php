<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Exception\InvalidArgumentException;

/**
 * Render-only pagination controls. No routing — supply URL callbacks.
 *
 * @example Pagination::make()->currentPage(2)->totalPages(10)->urlForPage(fn (int $p) => '/?page='.$p)
 */
final class Pagination extends Component
{
    use ResolvesTheme;

    private int $currentPage = 1;

    private int $totalPages = 1;

    /** @var (callable(int): string)|null */
    private $urlForPage = null;

    private string $previousLabel = 'Previous';

    private string $nextLabel = 'Next';

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    public function currentPage(int $page): self
    {
        $this->currentPage = max(1, $page);

        return $this;
    }

    public function totalPages(int $pages): self
    {
        $this->totalPages = max(1, $pages);

        return $this;
    }

    /**
     * @param callable(int): string $callback
     */
    public function urlForPage(callable $callback): self
    {
        $this->urlForPage = $callback;

        return $this;
    }

    public function previous(string $label): self
    {
        $this->previousLabel = $label;

        return $this;
    }

    public function next(string $label): self
    {
        $this->nextLabel = $label;

        return $this;
    }

    public function render(): string
    {
        if ($this->urlForPage === null) {
            throw new InvalidArgumentException(
                'Pagination requires urlForPage(callable). Example: Pagination::make()->urlForPage(fn (int $page): string => "/?page={$page}").',
            );
        }

        $items = [];

        $prevDisabled = $this->currentPage <= 1;
        $nextDisabled = $this->currentPage >= $this->totalPages;

        $items[] = $this->pageControl(
            $this->previousLabel,
            $prevDisabled ? null : ($this->urlForPage)($this->currentPage - 1),
            $prevDisabled,
            'previous',
        );

        $window = $this->pageWindow();

        foreach ($window as $page) {
            if ($page === null) {
                $items[] = sprintf(
                    '<span%s>…</span>',
                    $this->buildAttributeString($this->theme()->classes('pagination.ellipsis')),
                );
                continue;
            }

            $isCurrent = $page === $this->currentPage;
            $items[] = $this->pageControl(
                (string) $page,
                $isCurrent ? null : ($this->urlForPage)($page),
                false,
                $isCurrent ? 'current' : 'page',
            );
        }

        $items[] = $this->pageControl(
            $this->nextLabel,
            $nextDisabled ? null : ($this->urlForPage)($this->currentPage + 1),
            $nextDisabled,
            'next',
        );

        $attrs = $this->buildAttributeString(
            $this->theme()->classes('pagination'),
            ['role' => 'navigation', 'aria-label' => 'Pagination'],
        );

        return sprintf('<nav%s><ul%s>%s</ul></nav>', $attrs, $this->buildAttributeString($this->theme()->classes('pagination.list')), implode('', $items));
    }

    /**
     * @return list<int|null>
     */
    private function pageWindow(): array
    {
        $total = $this->totalPages;
        $current = $this->currentPage;

        if ($total <= 7) {
            return range(1, $total);
        }

        $pages = [1];

        if ($current > 3) {
            $pages[] = null;
        }

        foreach (range(max(2, $current - 1), min($total - 1, $current + 1)) as $page) {
            $pages[] = $page;
        }

        if ($current < $total - 2) {
            $pages[] = null;
        }

        $pages[] = $total;

        return $pages;
    }

    private function pageControl(string $label, ?string $href, bool $disabled, string $kind): string
    {
        $liAttrs = $this->buildAttributeString($this->theme()->classes('pagination.item', ['kind' => $kind]));

        if ($href === null || $disabled) {
            $spanAttrs = $this->buildAttributeString(
                $this->theme()->classes('pagination.link', ['disabled' => true, 'kind' => $kind, 'current' => $kind === 'current']),
                [
                    'aria-disabled' => $disabled || $kind === 'current' ? 'true' : null,
                    'aria-current' => $kind === 'current' ? 'page' : null,
                ],
            );

            return sprintf('<li%s><span%s>%s</span></li>', $liAttrs, $spanAttrs, $this->escape($label));
        }

        $aAttrs = $this->buildAttributeString(
            $this->theme()->classes('pagination.link', ['kind' => $kind]),
            ['href' => $href],
        );

        return sprintf('<li%s><a%s>%s</a></li>', $liAttrs, $aAttrs, $this->escape($label));
    }
}
