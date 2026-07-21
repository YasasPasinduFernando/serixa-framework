<?php

declare(strict_types=1);

namespace Serixa\Runtime;

use Serixa\Document\Asset\AssetManager;

/**
 * Progressive-enhancement client runtime registration helpers.
 *
 * Canonical script: assets/runtime/serixa.js (shipped with the framework).
 */
final class ClientRuntime
{
    public const SCRIPT_RELATIVE = 'assets/runtime/serixa.js';

    /**
     * Absolute filesystem path to the shipped runtime script.
     */
    public static function scriptPath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::SCRIPT_RELATIVE);
    }

    /**
     * Register the deferred runtime script on a document asset manager.
     *
     * @param string $publicUrl URL the browser will request (host app must serve the file).
     */
    public static function register(AssetManager $assets, string $publicUrl = '/assets/runtime/serixa.js'): AssetManager
    {
        return $assets->js($publicUrl, ['defer' => true]);
    }
}
