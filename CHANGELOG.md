# Changelog

All notable changes to SERIXA are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [v1.0.0-rc4] - 2026-07-22

Stabilization pass toward v1.0.0. No public API breaks.

### Added
- Semantic tokens: Border, Opacity, ZIndex, Breakpoint, Transition
- NullPerformanceProfiler, NullWidgetVisitor
- docs: styling catalog links, performance-v1, migration/upgrade/known-issues/release checklist & notes
- tools/benchmarks/v1-performance.php

### Changed
- Apps/examples migrate to Serixa\\Styling\\StyleSheet
- README / FAQ / installation aligned with offline Theme Compiler
- ThemeCompiler focus/hover/disabled use semantic token variables
- Website DocsCatalog: styling, assets, troubleshooting, performance

## [v1.0.0-rc3] — 2026-07-22

Offline CSS build pipeline. Removes Tailwind Play CDN from DocumentFactory and app layouts.

### Added
- `Serixa\Assets\StyleSheet` — offline merge of `framework/assets/css/serixa.css` + app CSS ? `public/assets/app.css`
- Committed base stylesheet `framework/assets/css/serixa.css` (no runtime compiler)
- Maintainer toolchain under `tools/css/` (Tailwind CLI used only to regenerate the base CSS)
- `docs/assets.md` — asset pipeline documentation
- `serixa build` writes `build/manifest.json`, `public/assets/manifest.json`, and `build/report.json`
- Layout `fill()` API on Row / Column / Center / Stack / Container for explicit width ownership
- `ClassMerger` resolves conflicting Tailwind utilities (later token wins)

### Changed
- `serixa build` compiles CSS, copies `serixa.js`, and prints a build report
- Website, starters, playground, validation apps, and examples register `/assets/app.css` via `StyleSheet::register()`
- Docs tutorials no longer show CDN script tags
- Generic Row / Column / Center / Stack no longer apply w-full by default (specialized layouts own sizing; Container still fills by default)

### Added (theme compiler)
- Serixa\\Styling\\* engine: StyleSheet, StyleCompiler, StyleRule, StyleSelector, StyleProperty, ThemeCompiler, CssWriter, CssOptimizer, ThemeManifest
- Theme tokens compile to CSS variables + .sx-* recipes (e.g. .sx-btn-primary)
- docs/styling.md

### Changed (theme compiler)
- Public stylesheet URL is /assets/serixa.css (BC alias still writes pp.css)
- serixa build runs ThemeCompiler + CssOptimizer before foundation merge
- DefaultTheme button variants emit semantic .sx-btn-* classes

### Removed
- Tailwind Play CDN (`cdn.tailwindcss.com`) and local Play CDN vendor script from the docs website

## [v1.0.0-rc1] — 2026-07-22

Ecosystem release candidate. Framework runtime API is unchanged from v0.12.0; this tag marks the first validated SERIXA 1.0 release candidate across the monorepo.

### Ecosystem (v0.1.0 packages)
- `serixa/icons`, `serixa/admin`, `serixa/forms`, `serixa/auth-ui`, `serixa/charts`, `serixa/calendar`, `serixa/devtools`, `serixa/cli`
- Starter kits: `serixa/starter-blog`, `serixa/starter-admin` (full); `serixa/starter-crm`, `serixa/starter-ecommerce`, `serixa/starter-portfolio`, `serixa/starter-docs` (scaffolds)

### Validation
- Framework QA: 114 tests, PHPStan clean, CS-Fixer clean
- Package QA: all eight official packages pass `composer qa`
- Playground render smoke: 6/6 pages
- Starter smoke: blog (12 routes), admin (16 routes)
- Validation apps: blog, admin-dashboard, crm — all pages render

### Fixed
- Playground `composer.lock` path-repository junction now targets `framework/` (was repo root)

## [v0.12.0] — 2026-07-22

### Changed
- **Breaking (packaging):** CLI moved to standalone `serixa/cli`. `framework` is runtime-only (widgets, renderer, theme, document, PE runtime).
- Removed `bin/serixa`, `src/Console/`, and `templates/` from this package.
- Suggested dependency: `composer require --dev serixa/cli`

## [v0.11.1] â€” 2026-07-22

Monorepo ecosystem layout. **No public API changes.**

### Changed
- Repository restructured: production package lives in `framework/`; optional `packages/*`, `starters/*`, and `tools/` added as skeletons
- Root `composer.json` is a private meta-package that delegates `test` / `qa` to `framework/`
- CLI reports `0.11.1`; runtime script version string aligned

### Added
- `docs/monorepo.md` â€” why monorepo, path repos, package lifecycle, split strategy
- Package skeletons: icons, charts, calendar, admin, auth-ui, devtools
- Starter skeletons: blog, admin, crm, ecommerce, portfolio, docs
- CI jobs for framework, package skeletons, starter skeletons, and root meta QA
- Additive `DefaultTheme` `icon` class map for `serixa/icons` (sizes + semantic colors)

## [v0.11.0] â€” 2026-07-22

Polish release following the pre-1.0 validation pass (`validation/REPORT.md`): performance, DX, and beginner documentation. No breaking changes.

### Added
- Beginner documentation: `docs/getting-started.md`, `docs/first-blog.md` (30-minute blog tutorial), `docs/first-dashboard.md`, `docs/project-structure.md`, `docs/deployment.md`, `docs/faq.md`, `docs/troubleshooting.md`
- `Padding::sm()` / `md()` / `lg()` shortcuts for common insets (`p-4` / `p-6` / `p-8`), alongside the existing fluent side setters
- `Column::alignStretch()` / `Row::alignStretch()` for explicit stretch alignment (matches the existing default, now settable fluently)
- `Scaffold::content()` as a readability alias for `Scaffold::body()`
- `Stack::clip()` to opt into `overflow-hidden` on layered content
- CLI: fast path for `serixa version` / `--version` / `-V` that skips command registration
- CLI: richer `serixa help` / no-argument banner with quick-start commands and doc links
- CLI: `serixa new` warns when run from inside the framework tree (path-repository recursion risk)
- Generated project `README.md` now links `docs/getting-started.md`, `docs/first-blog.md`, `docs/first-dashboard.md`, `docs/troubleshooting.md`
- Playground polish: minor demo/page cleanups following the validation pass

### Changed
- `DefaultTheme::classes()` now memoizes resolved class lists per component+state key; `token()` lookups are cached â€” addresses the ~0.8 ms/lookup cost measured in `docs/performance.md` (see `docs/performance-v011.md` for before/after tables)
- `WidgetRegistry::defaultMetadata()` memoizes the built-in metadata list
- `Serixa\Widget\Widget` shares one `HtmlEscaper`, `AttributeBag`, and `ClassMerger` instance across widget instances by default instead of constructing new ones per widget
- Client runtime (`assets/runtime/serixa.js`) only mounts an interactive module (modal, dropdown, tabs, accordion, toast, â€¦) when a matching marker is present in the DOM subtree being booted, instead of unconditionally mounting every module
- Validation error messages from layout/theme widgets (`Padding`, `Row`, `Column`, `DefaultTheme`) and pipeline passes now include actionable context
- `docs/cli.md` updated for `v0.11.0` and cross-linked to the new beginner docs
- `README.md`: new "Beginner path" section linking `getting-started.md` / `first-blog.md` / `first-dashboard.md`, expanded documentation index
- Playground demo extracts shared `PageSection` / `StatGrid` components to cut page duplication
- Generated project `README.md` stub expanded with setup, structure, and learning links

### Deprecated
- None

### Fixed
- None

### Performance
- Theme 3â€¯000 class lookups: ~2â€¯287 ms â†’ ~43 ms (~53Ã—) â€” see `docs/performance-v011.md`

## [v0.10.0] â€” 2026-07-22

### Added
- SERIXA CLI (`bin/serixa`) with `Application`, `CommandRegistry`, and pluggable commands
- Commands: `new`, `make:page`, `make:component`, `make:layout`, `make:theme`, `make:plugin`, `docs`, `dev`, `build`
- Filesystem template engine + `/templates` stubs (project / page / component / layout / theme / plugin)
- Project generator presets: `blog`, `crm`, `ecommerce`
- `Serixa\Console\Plugin::registerCommand()` for future packages
- Docs: `docs/cli.md`, `docs/scaffolding.md`, `docs/project-generator.md`, `docs/templates.md`

### Changed
- Composer `bin` exposes `serixa`

### Deprecated
- None

### Fixed
- None

## [v0.9.0] â€” 2026-07-22

### Added
- Interactive overlays: `Modal`, `Drawer`, `Popover`, `Tooltip`
- Feedback: `Toast`, `Alert`, `Badge`, `Chip`, `Spinner`, `Progress`, `Skeleton`
- Navigation: `Dropdown`, `DropdownMenu`, `DropdownItem`, `Tabs`, `Tab`, `TabPanel`
- Disclosure: `Accordion`, `AccordionItem`
- Modular client runtime modules in `assets/runtime/serixa.js` (`Serixa.module`)
- Docs: `docs/interactive-components.md`, `docs/runtime-modules.md`, `examples/interactive.php`
- Playground Interactive page + Dashboard/CRUD/Settings/Profile demos

### Changed
- Client runtime version `0.9.0` with focus trap, keyboard nav, and overlay helpers

### Deprecated
- None

### Fixed
- None

## [v0.8.1] â€” 2026-07-22

### Fixed
- **Attribute leakage on multi-node form widgets:** `Checkbox`, `Radio`, `SwitchControl`, and `SearchField` no longer copy widget `id` / `class` / custom attributes / a11y / PE event metadata onto wrappers, nested labels, or icon chrome.
- **Duplicate IDs:** labeled checkboxes/radios/switches no longer emit the same `id` on both the control and the wrapper/label.
- **SearchField `role` ownership:** `role="search"` stays on the wrapper once; `role="searchbox"` and event metadata stay on the `<input>` only.
- **`HasFormControl` id ownership:** `id` is applied once via widget chrome on the interactive element (plus explicit derived ids for unlabeled fallbacks), not re-declared inside the shared form-control attribute map.

### Added
- Regression coverage in `tests/Component/FormAttributeIsolationTest.php`

### Changed
- None (public fluent APIs unchanged)

### Deprecated
- None

## [v0.8.0] â€” 2026-07-22

### Added
- Progressive enhancement: fluent DOM events (`onClick`, `onChange`, `onInput`, `onSubmit`, `onFocus`, `onBlur`, `onKeyDown`, `onKeyUp`) â†’ `data-serixa-on-*`
- HTML constraint helpers on form controls: `readonly()`, `min()`, `max()`, `minLength()`, `maxLength()`, `pattern()`
- Client runtime: `assets/runtime/serixa.js` + `Serixa\Runtime\ClientRuntime`
- `Serixa\Event\EventRegistry` for handler metadata / future plugins
- Docs: `docs/events.md`, `docs/progressive-enhancement.md`, `examples/events.php`
- Playground Login / CRUD demonstrate event metadata (no JS business logic)

### Changed
- Form controls and Button/Form emit optional PE attributes; SSR behavior unchanged without JS

### Deprecated
- None

### Fixed
- None

## [v0.7.0] â€” 2026-07-22

### Added
- Data display: `DataTable`, `Table`, `TableHead`, `TableBody`, `TableRow`, `TableCell`, `TableHeaderCell`
- `DataTable` dual API: convenience `headers()`/`rows()` and composition `child(Table)` with `Table::head()` / `body()`
- Application widgets: `SearchField`, `Pagination`, `PageHeader`, `Toolbar`, `EmptyState`
- Media: `Avatar`, `Image`, `Logo`
- Dashboard: `StatCard`, `Metric`, `ActivityList`, `ActivityItem`
- Docs: `docs/data-display.md`, `examples/dashboard.php`, `examples/table.php`, `examples/profile.php`
- Playground CRUD / Dashboard / Profile / Login updated to remove data-display placeholders

### Changed
- `WidgetRegistry` / `DefaultTheme` cover all v0.7.0 widgets
- Playground Dashboard uses `Metric`; Profile uses `Logo` + `Image` + `Avatar`

### Deprecated
- None

### Fixed
- None

## [v0.6.0] â€” 2026-07-22

### Added
- UI Foundation chrome: `AppBar`, `Sidebar`, `MainContent`, `Footer` (compose with `Scaffold`)
- Forms: `Form`, `Label`, `Input`, `PasswordInput`, `Textarea`, `Checkbox`, `Radio`, `SwitchControl`, `Select`
- Navigation: `Link`
- Field messaging: `FieldGroup`, `HelperText`, `ErrorText`
- `HasFormControl` shared concern (`name`, `id`, `disabled`, `required`, `invalid`, `describedBy`)
- Docs: `docs/ui-foundation.md`, `examples/forms.php`, `examples/scaffold.php`
- PHPUnit coverage for all new widgets

### Changed
- `Divider` supports `orientation()`, `spacing()`, and optional `label()` (aria-label)
- `WidgetRegistry` / `DefaultTheme` include UI Foundation widgets

### Deprecated
- None

### Fixed
- None

## [v0.5.2] â€” 2026-07-22

### Added
- Fluent `Padding` builder: `Padding::make()->all(4)->child(...)` / `->x()` / `->y()` / side setters
- `Scaffold` layout widget (`appBar`, `sidebar`, `body`, `footer`)
- Canonical PSR-4 namespace `Serixa\`
- Docs: `docs/NAMESPACE.md`, ADR 0006

### Changed
- Composer autoload root is `Serixa\`
- Theme maps `scaffold*` class keys for the new shell widget

### Deprecated
- Static `Padding::all()` / `Padding::x()` / â€¦ factories (use fluent `Padding::make()->â€¦`; still work via `__callStatic`)

### Fixed
- None

## [v0.5.1] â€” 2026-07-21

### Added
- Exception hierarchy: `SerixaException`, `InvalidArgumentException`, `ContainerException`, `PipelineException`, `RegistryException`, `PluginException`
- Coding standards: `.editorconfig`, `.php-cs-fixer.dist.php`, `phpstan.neon.dist`
- GitHub Actions CI (PHPUnit + PHPStan + CS dry-run)
- Governance docs: `docs/API_STABILITY.md`, `docs/VERSIONING.md`, `docs/DEPRECATION.md`, `docs/CODING_STANDARDS.md`
- Architecture Decision Records under `docs/adr/`
- `@internal` markers on pipeline passes, component concerns, `HeadElement`

### Changed
- Container/pipeline/registry/plugin/theme errors use SERIXA exception types (still subclasses of SPL exceptions for BC)
- Composer scripts: `phpstan`, `cs-check`, `cs-fix`, `qa`

### Deprecated
- None (existing Component aliases unchanged)

### Fixed
- None

## [v0.5.0] â€” 2026-07-21

### Added
- Document engine: `Document`, `Html`, `Head`, `Body`
- Head elements: `Title`, `Meta`, `Link`, `Script`, `Style`
- `SeoManager` â€” title, description, keywords, canonical, robots, author, theme-color, favicon, Open Graph, Twitter Cards, JSON-LD placeholder
- Asset architecture: `AssetManager`, `AssetBundle`, `AssetManifest`, `AssetResolver` (versioning, defer, preload, prefetch â€” no Vite)
- `DocumentRenderer` for full HTML pages
- `DocumentInterface`; `Renderer` detects documents vs fragments
- Docs: `docs/document-engine.md`, `examples/document.php`, Document PHPUnit suite

### Changed
- `RendererInterface::render()` accepts `DocumentInterface|WidgetInterface|string` (additive)
- Roadmap: Milestone 5 marked complete

### Deprecated
- None

### Fixed
- None

## [v0.4.0] â€” 2026-07-21

### Added
- Runtime Core: `WidgetContext`, `RenderContext`
- Service `Container` (`bind`, `singleton`, `instance`, `resolve` + constructor autowiring)
- `WidgetRegistry` and `WidgetMetadata` with built-in widget catalog
- Replaceable `RenderPipeline` with passes: build tree, resolve context, lifecycle, theme resolution, accessibility, render
- `HtmlWriter` for HTML fragment assembly
- Plugin contracts: `PluginInterface`, `WidgetPlugin`, `ThemePlugin`, `RuntimePlugin`, `PluginManager`
- Inspector contracts and snapshots: `WidgetInspector`, `TreeInspector`, `WidgetSnapshot`, `RenderSnapshot`, `PerformanceSnapshot`
- Performance contracts: `PerformanceProfiler`, `RenderProfiler`, `MemoryProfiler`
- Docs: `docs/runtime-core.md`, `examples/runtime.php`, Runtime PHPUnit suite

### Changed
- `Renderer` is now a pipeline coordinator (public render APIs preserved)
- Roadmap renumbered upcoming milestones after Runtime Core

### Deprecated
- None

### Fixed
- None

## [v0.3.0] â€” 2026-07-21

### Added
- Widget vocabulary: `WidgetInterface`, abstract `Widget`, `WidgetCollection`, `WidgetFactory`, `WidgetVisitor`
- Widget tree engine: `WidgetTree`, `TreeNode`, `TreeWalker`, `TreeVisitor`, `LifecycleTreeVisitor`
- Lifecycle hooks: `beforeMount()`, `mounted()`, `beforeRender()`, `afterRender()`, `destroy()`
- Renderer integration with tree + lifecycle (`renderWidget()`, `tree()`)
- Memory-only state engine: `StateManager`, `StateInterface`, `WidgetState`, `StateBuilder`, `StateResolver`
- Design tokens: `DesignToken`, `ColorToken`, `SpacingToken`, `RadiusToken`, `ShadowToken`, `TypographyToken`
- `ThemeInterface::token()` and token composition inside `DefaultTheme`
- Event preparation: `EventInterface`, `ClickEvent`, `HoverEvent`, `FocusEvent`, `WidgetEventDispatcher`, `NullWidgetEventDispatcher`
- `AccessibilityBuilder` with ARIA helpers merged into widget attributes
- Shared `AssertsOption` concern
- Foundation PHPUnit suite and `examples/foundations.php`
- Project docs: `ROADMAP.md`, architecture overview, expanded README

### Changed
- Renderer now builds a `WidgetTree` and runs lifecycle visitors before/after paint
- `DefaultTheme` resolves visual styles via design tokens where applicable
- Package identity: Composer name `serixa/framework` (PSR-4 namespace remains `Serixa\` for BC)

### Deprecated
- Prefer `Serixa\Widget\Widget` over `Serixa\Component\Component` (alias retained)
- Prefer `WidgetInterface` over `ComponentInterface` (interface retained as extension alias)
- Prefer `Renderer::renderWidget()`; `renderComponent()` remains as alias

### Fixed
- TreeNode static/instance naming clash (`forWidget` / `forText` factories)
- Component BC alias now correctly implements `ComponentInterface`

## [v0.2.0] â€” 2026-07-21

### Added
- Layout widgets: Row, Column, Center, Padding, Spacer, Expanded, SizedBox, Stack, Divider
- Theme seam: ThemeInterface, DefaultTheme, ThemeManager
- Button fluent aliases: `primary()`, `secondary()`, `danger()`, `ghost()`, `small()`, `large()`, `rounded()`
- Layout documentation and tests

### Changed
- Milestone 1 widgets resolve CSS classes through ThemeManager (public APIs preserved)

## [v0.1.0] â€” 2026-07-21

### Added
- Initial rendering engine: Component base, Renderer, Button, Card, Text, Container
- Support utilities: HtmlEscaper, AttributeBuilder, ClassMerger
- PHPUnit suite and basic example
