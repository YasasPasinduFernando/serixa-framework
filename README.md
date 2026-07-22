# SERIXA Framework

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg)](https://www.php.net/)

Composer package: **`serixa/framework`**

SERIXA is a Flutter-inspired PHP UI framework: compose widgets in PHP, render semantic HTML, apply a theme, and ship offline CSS plus a tiny progressive-enhancement runtime.

This folder is the **runtime engine** inside the [SERIXA monorepo](https://github.com/YasasPasinduFernando/serixa). Scaffolding lives in [`serixa/cli`](../packages/cli/).

## Requirements

- PHP **8.2+**
- Extensions: `json`, `mbstring`

## Install

```bash
composer require serixa/framework
composer require --dev serixa/cli   # optional scaffolding
```

```php
use Serixa\Component\Button;
use Serixa\Component\Column;
use Serixa\Component\Text;
use Serixa\Document\Document;
use Serixa\Document\DocumentRenderer;

echo DocumentRenderer::make()->render(
    Document::make()
        ->title('Hello SERIXA')
        ->body(
            Column::make(
                Text::make('Welcome'),
                Button::make('Get started')->primary(),
            ),
        ),
);
```

Official guides: [Installation](../docs/installation.md) · [Getting started](../docs/getting-started.md) · [Styling](../docs/styling.md) · [Accessibility](../docs/accessibility.md)

## Develop here

```bash
composer install
composer qa
php examples/basic.php
```

## Security

See [SECURITY.md](../SECURITY.md).

## License

MIT — see [LICENSE](LICENSE).
