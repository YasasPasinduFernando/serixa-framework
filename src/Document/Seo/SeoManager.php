<?php

declare(strict_types=1);

namespace Serixa\Document\Seo;

use Serixa\Document\Element\Link;
use Serixa\Document\Element\Meta;
use Serixa\Document\Element\Script;
use Serixa\Document\Element\Title;
use Serixa\Document\Head;

/**
 * Builds semantic SEO head elements (title, meta, OG, Twitter, JSON-LD placeholder).
 */
final class SeoManager
{
    private string $title = '';

    private string $description = '';

    /** @var list<string> */
    private array $keywords = [];

    private ?string $canonical = null;

    private ?string $author = null;

    private string $robots = 'index,follow';

    private ?string $themeColor = null;

    private ?string $favicon = null;

    /** @var array<string, string> */
    private array $openGraph = [];

    /** @var array<string, string> */
    private array $twitter = [];

    private ?string $jsonLd = null;

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param list<string> $keywords
     */
    public function keywords(array $keywords): self
    {
        $this->keywords = array_values($keywords);

        return $this;
    }

    public function canonical(string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    public function author(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function robots(string $robots): self
    {
        $this->robots = $robots;

        return $this;
    }

    public function themeColor(string $color): self
    {
        $this->themeColor = $color;

        return $this;
    }

    public function favicon(string $href): self
    {
        $this->favicon = $href;

        return $this;
    }

    public function openGraph(string $property, string $content): self
    {
        $this->openGraph[$property] = $content;

        return $this;
    }

    public function twitter(string $name, string $content): self
    {
        $this->twitter[$name] = $content;

        return $this;
    }

    /**
     * Placeholder for structured data (author-provided JSON string).
     */
    public function jsonLd(string $json): self
    {
        $this->jsonLd = $json;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return list<string>
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function applyTo(Head $head): void
    {
        if ($this->title !== '') {
            $head->title(Title::make($this->title));
        }

        $head->meta(Meta::charset());
        $head->meta(Meta::viewport());

        if ($this->description !== '') {
            $head->meta(Meta::name('description', $this->description));
        }

        if ($this->keywords !== []) {
            $head->meta(Meta::name('keywords', implode(', ', $this->keywords)));
        }

        if ($this->author !== null) {
            $head->meta(Meta::name('author', $this->author));
        }

        $head->meta(Meta::name('robots', $this->robots));

        if ($this->themeColor !== null) {
            $head->meta(Meta::name('theme-color', $this->themeColor));
        }

        if ($this->canonical !== null) {
            $head->link(Link::canonical($this->canonical));
        }

        if ($this->favicon !== null) {
            $head->link(Link::icon($this->favicon));
        }

        $ogDefaults = [];

        if ($this->title !== '') {
            $ogDefaults['og:title'] = $this->title;
        }

        if ($this->description !== '') {
            $ogDefaults['og:description'] = $this->description;
        }

        foreach (array_merge($ogDefaults, $this->openGraph) as $property => $content) {
            $head->meta(Meta::property($property, $content));
        }

        $twitterDefaults = ['twitter:card' => 'summary'];

        if ($this->title !== '') {
            $twitterDefaults['twitter:title'] = $this->title;
        }

        if ($this->description !== '') {
            $twitterDefaults['twitter:description'] = $this->description;
        }

        foreach (array_merge($twitterDefaults, $this->twitter) as $name => $content) {
            $head->meta(Meta::name($name, $content));
        }

        if ($this->jsonLd !== null) {
            $head->script(Script::jsonLd($this->jsonLd));
        }
    }
}
