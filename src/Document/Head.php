<?php

declare(strict_types=1);

namespace Serixa\Document;

use Serixa\Document\Element\HeadElement;
use Serixa\Document\Element\Link;
use Serixa\Document\Element\Meta;
use Serixa\Document\Element\Script;
use Serixa\Document\Element\Style;
use Serixa\Document\Element\Title;

/**
 * Document &lt;head&gt; â€” collects title, meta, links, styles, and scripts.
 */
final class Head
{
    private ?Title $title = null;

    /** @var list<HeadElement> */
    private array $elements = [];

    public static function make(): self
    {
        return new self();
    }

    public function title(string|Title $title): self
    {
        $this->title = $title instanceof Title ? $title : Title::make($title);

        return $this;
    }

    public function add(HeadElement $element): self
    {
        $this->elements[] = $element;

        return $this;
    }

    public function meta(Meta $meta): self
    {
        return $this->add($meta);
    }

    public function link(Link $link): self
    {
        return $this->add($link);
    }

    public function style(Style $style): self
    {
        return $this->add($style);
    }

    public function script(Script $script): self
    {
        return $this->add($script);
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    /**
     * @return list<HeadElement>
     */
    public function elements(): array
    {
        return $this->elements;
    }

    public function render(): string
    {
        $html = '<head>';

        if ($this->title !== null) {
            $html .= $this->title->render();
        }

        foreach ($this->elements as $element) {
            $html .= $element->render();
        }

        return $html . '</head>';
    }
}
