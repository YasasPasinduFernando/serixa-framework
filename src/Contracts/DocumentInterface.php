<?php

declare(strict_types=1);

namespace Serixa\Contracts;

use Serixa\Document\Asset\AssetManager;
use Serixa\Document\Seo\SeoManager;

/**
 * A full HTML document (not a fragment).
 */
interface DocumentInterface
{
    public function seo(): SeoManager;

    public function assets(): AssetManager;

    public function getLanguage(): string;

    /**
     * @return list<\Serixa\Contracts\WidgetInterface|string>
     */
    public function bodyChildren(): array;
}
