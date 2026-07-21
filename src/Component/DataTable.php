<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Component\Concerns\ResolvesTheme;
use Serixa\Contracts\WidgetInterface;
use Serixa\Exception\InvalidArgumentException;

/**
 * High-level data table composer. Supports convenience headers/rows and composition via child(Table).
 *
 * @example DataTable::make()->headers(['Name'])->rows([['Ada'], [Link::make('Edit')->href('#')]])
 * @example DataTable::make()->child(Table::make()->head(...)->body(...))
 */
final class DataTable extends Component
{
    use ResolvesTheme;

    /** @var list<string|WidgetInterface> */
    private array $headers = [];

    /** @var list<list<string|WidgetInterface>> */
    private array $rows = [];

    private bool $striped = false;

    private bool $hover = true;

    private bool $compact = false;

    private ?WidgetInterface $emptyState = null;

    private ?string $caption = null;

    private ?Table $composedTable = null;

    private function __construct()
    {
        parent::__construct();
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param list<string|WidgetInterface> $headers
     */
    public function headers(array $headers): self
    {
        $this->headers = array_values($headers);

        return $this;
    }

    /**
     * @param list<list<string|WidgetInterface>> $rows
     */
    public function rows(array $rows): self
    {
        $normalized = [];

        foreach ($rows as $row) {
            $normalized[] = array_values($row);
        }

        $this->rows = $normalized;

        return $this;
    }

    /**
     * Composition API: wrap a manually built {@see Table}.
     *
     * Convenience {@see headers()} / {@see rows()} are ignored when a composed table is set.
     */
    public function child(WidgetInterface|string $child): self
    {
        if (! $child instanceof Table) {
            throw new InvalidArgumentException('DataTable::child() accepts a Table widget only.');
        }

        $this->composedTable = $child;

        return $this;
    }

    public function striped(bool $striped = true): self
    {
        $this->striped = $striped;

        return $this;
    }

    public function hover(bool $hover = true): self
    {
        $this->hover = $hover;

        return $this;
    }

    public function compact(bool $compact = true): self
    {
        $this->compact = $compact;

        return $this;
    }

    public function emptyState(WidgetInterface $emptyState): self
    {
        $this->emptyState = $emptyState;

        return $this;
    }

    public function caption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function childrenForTree(): array
    {
        if ($this->composedTable !== null) {
            $nodes = [$this->composedTable];

            if ($this->emptyState !== null) {
                $nodes[] = $this->emptyState;
            }

            return $nodes;
        }

        $nodes = $this->headers;

        foreach ($this->rows as $row) {
            foreach ($row as $cell) {
                $nodes[] = $cell;
            }
        }

        if ($this->emptyState !== null) {
            $nodes[] = $this->emptyState;
        }

        return $nodes;
    }

    public function render(): string
    {
        if ($this->composedTable !== null) {
            return $this->renderComposed();
        }

        if ($this->rows === [] && $this->emptyState !== null) {
            return $this->renderChild($this->emptyState);
        }

        $headerCells = [];

        foreach ($this->headers as $header) {
            $headerCells[] = TableHeaderCell::make($header);
        }

        $bodyRows = [];

        foreach ($this->rows as $row) {
            $cells = [];

            foreach ($row as $cell) {
                $cells[] = TableCell::make($cell);
            }

            $bodyRows[] = TableRow::make($cells);
        }

        $state = $this->tableState();

        $captionHtml = '';

        if ($this->caption !== null) {
            $captionHtml = sprintf(
                '<caption%s>%s</caption>',
                $this->buildAttributeString($this->theme()->classes('dataTable.caption')),
                $this->escape($this->caption),
            );
        }

        $headHtml = sprintf(
            '<thead%s>%s</thead>',
            $this->buildAttributeString($this->theme()->classes('tableHead')),
            $this->renderChild(TableRow::make($headerCells)),
        );

        $bodyHtml = sprintf(
            '<tbody%s>%s</tbody>',
            $this->buildAttributeString($this->theme()->classes('tableBody')),
            $this->renderChildren($bodyRows),
        );

        $wrapperAttrs = $this->buildAttributeString(
            $this->theme()->classes('dataTable', $state),
            ['role' => 'region', 'aria-label' => $this->caption ?? 'Data table'],
        );

        $tableAttrs = $this->buildAttributeString($this->theme()->classes('table', $state));

        return sprintf(
            '<div%s><div%s><table%s>%s%s%s</table></div></div>',
            $wrapperAttrs,
            $this->buildAttributeString($this->theme()->classes('dataTable.scroll')),
            $tableAttrs,
            $captionHtml,
            $headHtml,
            $bodyHtml,
        );
    }

    private function renderComposed(): string
    {
        assert($this->composedTable !== null);

        $table = $this->composedTable
            ->striped($this->striped)
            ->hover($this->hover)
            ->compact($this->compact);

        $state = $this->tableState();

        $wrapperAttrs = $this->buildAttributeString(
            $this->theme()->classes('dataTable', $state),
            ['role' => 'region', 'aria-label' => $this->caption ?? 'Data table'],
        );

        return sprintf(
            '<div%s><div%s>%s</div></div>',
            $wrapperAttrs,
            $this->buildAttributeString($this->theme()->classes('dataTable.scroll')),
            $this->renderChild($table),
        );
    }

    /**
     * @return array{striped: bool, hover: bool, compact: bool}
     */
    private function tableState(): array
    {
        return [
            'striped' => $this->striped,
            'hover' => $this->hover,
            'compact' => $this->compact,
        ];
    }
}
