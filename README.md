# SERIXA Framework

Composer package: **`serixa/framework`**

PHP UI framework (SSR widgets → HTML). Namespace: `Serixa\`.

This folder is the **runtime engine** inside the [SERIXA monorepo](../docs/monorepo.md). Scaffolding lives in [`serixa/cli`](../packages/cli/).

## Install

```bash
composer require serixa/framework
composer require --dev serixa/cli   # optional scaffolding
```

Official guide: [Installation Guide](../docs/installation.md) (Composer, first page, rendering, widgets, themes, layouts).

## Develop here

```bash
composer install
composer qa
php examples/basic.php
```

## Docs

Ecosystem docs: [`docs/`](../docs/). CLI: [`docs/cli.md`](../docs/cli.md).
