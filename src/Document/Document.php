<?php

declare(strict_types=1);

namespace Serixa\Document;

use Serixa\Contracts\DocumentInterface;
use Serixa\Contracts\WidgetInterface;
use Serixa\Document\Asset\AssetManager;
use Serixa\Document\Seo\SeoManager;

/**
 * Fluent full-page document builder.
 *
 * @example Document::make()->title('Dashboard')->body(Text::make('Hi'))
 */
final class Document implements DocumentInterface
{
    private SeoManager $seo;

    private AssetManager $assets;

    private Body $body;

    private string $language = 'en';

    private string $dir = 'ltr';

    private function __construct()
    {
        $this->seo = new SeoManager();
        $this->assets = new AssetManager();
        $this->body = Body::make();
    }

    public static function make(): self
    {
        return new self();
    }

    public function title(string $title): self
    {
        $this->seo->title($title);

        return $this;
    }

    public function description(string $description): self
    {
        $this->seo->description($description);

        return $this;
    }

    /**
     * @param list<string> $keywords
     */
    public function keywords(array $keywords): self
    {
        $this->seo->keywords($keywords);

        return $this;
    }

    public function themeColor(string $color): self
    {
        $this->seo->themeColor($color);

        return $this;
    }

    public function language(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function dir(string $dir): self
    {
        $this->dir = $dir;

        return $this;
    }

    public function canonical(string $url): self
    {
        $this->seo->canonical($url);

        return $this;
    }

    public function author(string $author): self
    {
        $this->seo->author($author);

        return $this;
    }

    public function robots(string $robots): self
    {
        $this->seo->robots($robots);

        return $this;
    }

    public function favicon(string $href): self
    {
        $this->seo->favicon($href);

        return $this;
    }

    public function openGraph(string $property, string $content): self
    {
        $this->seo->openGraph($property, $content);

        return $this;
    }

    public function twitter(string $name, string $content): self
    {
        $this->seo->twitter($name, $content);

        return $this;
    }

    public function jsonLd(string $json): self
    {
        $this->seo->jsonLd($json);

        return $this;
    }

    public function body(WidgetInterface|string|Body ...$nodes): self
    {
        if (count($nodes) === 1 && $nodes[0] instanceof Body) {
            $this->body = $nodes[0];

            return $this;
        }

        $this->body = Body::make();

        foreach ($nodes as $node) {
            if ($node instanceof Body) {
                foreach ($node->getChildren() as $child) {
                    $this->body->child($child);
                }
                continue;
            }

            $this->body->child($node);
        }

        return $this;
    }

    public function assets(): AssetManager
    {
        return $this->assets;
    }

    public function withAssets(AssetManager $assets): self
    {
        $this->assets = $assets;

        return $this;
    }

    public function seo(): SeoManager
    {
        return $this->seo;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function textDirection(): string
    {
        return $this->dir;
    }

    public function getBody(): Body
    {
        return $this->body;
    }

    /**
     * @return list<WidgetInterface|string>
     */
    public function bodyChildren(): array
    {
        return $this->body->getChildren();
    }
}
