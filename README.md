# xStyles 1.5

Preview how the current theme styles every common page element.

xStyles is a living style guide for XOOPS themes. It displays repeatable examples
using the active theme's real styles and form renderer. Designers may call this a
"CSS specimen page": a collection of elements for checking their appearance together.
The module creates no content tables, sample records or upload folders.

## Requirements

- XOOPS 2.7 or newer, with XMF and the core form-renderer API
- PHP 8.2 or newer
- Smarty 4 or 5 through the corresponding XOOPS core integration

Isolated integration checks cover XOOPS 2.7.4 / Smarty 4.5.8 and XOOPS 2.8 /
Smarty 5 using real core classes, templates, lifecycle callbacks and admin
controllers without a database. A full database-backed installation/upgrade
through the XOOPS 2.7 administrator still needs release testing.

## Use

Start with the [illustrated tutorial](docs/TUTORIAL.md) for beginner, advanced and
theme-designer walkthroughs.

Install or update through System > Modules, then open `/modules/xstyles/`.
The combined gallery contains Typography, Content, Tables, Forms, Comments,
Blocks and menus, Bootstrap 5, Bootstrap 6 Alpha, and optional Theme examples. Each example has a section permalink and expandable,
escaped rendered HTML. Copy works through the Clipboard API; on HTTP or where
clipboard access is denied, the source is selected for manual copying.
The example filter searches titles, selectors, sample text and rendered markup
within the current section. Escape clears it. Following a permalink to a hidden
example clears the filter so the destination remains visible.

Useful URLs:

- `index.php?section=forms`
- `index.php?section=typography&mode=dark&direction=rtl`
- `index.php?section=blocks&zones=all`
- `index.php?section=blocks&zones=untitled`
- `index.php?section=blocks&zones=empty`

Theme and layout previews lists only themes enabled for visitors by the site.
Theme selection uses `xs_theme` for this request only; it does not change the
visitor's normal theme. Section links and responsive frames retain that preview.
This requires the core request-only module theme override (available in this
XOOPS 2.8 tree); older cores keep normal rendering and omit theme preview controls.
On the tested XOOPS 2.7.4 core, the frontend shows the current theme with layout,
renderer and responsive previews; frontend theme switching and side-by-side
theme comparison are unavailable. **Admin styles** can still preview and compare
admin themes without activating them.
Colour mode and direction previews do not edit theme files or site settings.
Colour mode synchronizes `data-bs-theme`, `data-theme`, `data-mt-mode` and
`color-scheme`, including after theme initialization and system preference
changes. An optional descriptor can specify other data attributes. Explicit
preview mode owns those attributes until **Theme default** is selected; it does
not write localStorage or publish settings. Native theme buttons can still save
their own preferences while a preview controls the display. Themes still need their own mode CSS.
Direction simulation changes only `dir`: it does not load another
language's RTL stylesheet or select another form renderer: use the site's
language selector to test a real RTL locale.

The zone preview replaces the request's block arrays with populated, untitled
or empty fixtures. It never inserts or changes database blocks. A theme must
implement a zone for it to appear. Choose **Keep site blocks** to return to
ordinary rendering. Synthetic block IDs cannot refer to real editable blocks.

Forms accept sample input for appearance testing. xStyles has no submission
handler or upload action. JavaScript prevents example submissions; without
JavaScript they return to the read-only gallery. Do not enter real passwords
or private data. Example menu links and confirmation actions are neutralized.

When cssHolmes is active and the visitor has its overlay permission, the
gallery links to its accessibility, HTML5 and layout profiles using its
configured query key. xStyles neither grants permissions nor duplicates the
inspector.

## Templates and assets

All templates are registered in `xoops_version.php` and resolved through the
core `db:` resource, so theme overrides under
`themes/<theme>/modules/xstyles/` work normally. Update the module after
deploying new templates, particularly on older cores or custom template sets.

The gallery's scoped CSS styles its navigation, captions and source controls.
Examples inherit the active theme. Core menus keep their output with fixture
ID prefixes and inert links so they can coexist with the site's real blocks.
Comments use fixed sample data and suppress reply/moderation actions.

Gallery images live in `assets/images/`. Unused root icons, duplicate images,
the old `extras` stylesheet and the design prototype are no longer distributed.
Custom templates using old root image URLs must use the maintained assets instead.
Legacy xLanguage's `XLANGUAGE_CONFIG_FILE` marker is retained.

## Verification

Install development tools once in `xoops_lib` using its development
`composer.json`. The separate `composer.dist.json` remains for runtime dependencies.
xStyles needs no module-local Composer manifest or vendor directory.
From the installed module directory, run:

```text
php ../../xoops_lib/vendor/bin/php-cs-fixer fix --dry-run --diff --config=.php-cs-fixer.dist.php
php ../../xoops_lib/vendor/bin/phpstan analyse --configuration=phpstan.neon.dist --memory-limit=512M
php ../../xoops_lib/vendor/bin/rector process --dry-run
php ../../xoops_lib/vendor/bin/phpunit --configuration=phpunit.xml.dist
```

The XOOPS module-devops quality gate runs PHP-CS-Fixer, PHPStan level 8,
Rector in dry-run mode and PHPUnit. The last command runs PHPUnit only.
For a separate checkout, replace `../../xoops_lib/vendor/bin/` with the full path
to the shared tools, keeping the module directory as your working directory.
Omit `--dry-run` from PHP-CS-Fixer when applying formatting corrections.
The initial PHPStan baseline records existing findings, including missing XOOPS
runtime declarations; it is a starting point for reducing analysis debt, not a
claim that every runtime API is statically checked. Do not regenerate it to hide
new findings.

Five standalone regression scripts run through PHPUnit without a configured
site. Four integration scripts run when `XSTYLES_CORE` points to a compatible
XOOPS document root; otherwise PHPUnit reports them as skipped. The fixture
harness supports both Smarty 4 and 5. Individual checks remain available:

```text
php tests/gallery.php
php -d zend.assertions=1 -d assert.exception=1 tests/lifecycle.php
php tests/fixtures.php C:/path/to/xoops/htdocs
node --check assets/js/gallery.js
```

The fixture integration test uses real XOOPS classes with isolated temporary
caches and a test-only template resource, without starting the site session
or opening its database. It checks section filtering, core output, template
assignment isolation, IDs and inert example actions.

Browser checks should cover narrow/wide viewports, keyboard operation, the
active renderer, source/copy, RTL language selection and theme overrides.

## GitHub automation

The repository is [XoopsModules27x/xstyles](https://github.com/XoopsModules27x/xstyles).
GitHub Actions runs the quality gate on PHP 8.2, 8.3, 8.4 and 8.5, plus JavaScript
syntax checks. Version tags run the same gate before attaching an installable ZIP.
The archive includes the tutorial and its screenshots, and excludes development
tools, dependencies, tests and private `docs/00/` notes. Installing the module
does not require Composer.

CI installs its tools in a temporary directory outside the module checkout.
Dependabot checks Actions weekly; shared PHP dependency updates belong to the
development tools installation. Automatic merging
is off unless maintainers enable the repository's auto-merge feature, configure
required checks and set `XOOPS_DEPENDABOT_AUTOMERGE=true`. Optional SonarCloud
analysis requires the matching project and a `SONAR_TOKEN` repository secret.

## Maintenance

Install/update/uninstall callbacks do not remove historical uploads, templates
or permission records left by older scaffolding. Remove such files manually
only after checking that they are no longer needed. Administration includes an
Home dashboard, XMF About page and **Admin theme examples** gallery. Home shows the
standard module shortcuts, current requirements and server status. These pages perform
no folder creation or sample-data work.

The Bootstrap 5 and Bootstrap 6 Alpha sections use the selected theme's assets;
xStyles does not load a framework. Bootstrap 6 examples target `6.0.0-alpha.1`,
including its native dialog/drawer and menu components. The host theme must
expose its ESM components as `window.bootstrap`, as `mytheme6` does. Alpha APIs
may change. Missing or incompatible JavaScript is reported beside interactive
samples. Renderer preview changes only example form markup, then restores the
original site renderer, including after failures. Advanced samples include
11/12-option selects, multiselect, color, checkbox layouts and a two-tab form.
Existing core renderer defects remain visible rather than being patched by the
gallery. A notice lists unmatched core form label targets detected in the actual
rendered output and disappears when those targets are fixed. Repeatable screenshot
capture/comparison is available in the test tools. CSS diagnostics belong to cssHolmes.

Theme authors can register their own local templates through a bounded JSON
descriptor; see [Add your own theme examples](docs/TUTORIAL.md#add-your-own-theme-examples). Templates are trusted
installed theme code, not uploaded user content. Each renders with isolated
assignments and its own ID prefix. A failed template does not hide other examples.
The source panel shows **rendered preview HTML**, including adjusted IDs and actions.

## Shared gallery workspace

The public gallery and **Admin styles** share the same navigation, rounded controls
and HTML source panels. Choose a category, then an element in the navigator to
focus its preview. Search filters the current category; Escape clears the search.
**Preview details** contains the theme, language and renderer information.
Use **Single preview** or **Compare themes** to change workspace views. Returning
to Single preview retains the selected element. Without JavaScript, all examples
remain available as a normal page. Workspace styling does not restyle the example
forms or buttons: those continue to show the selected theme's actual appearance.

## Responsive previews and migration

Expand **Responsive preview** to load one 360/768/1280 CSS-pixel iframe, or choose
**Compare all widths**. Frames retain the full theme and published cssHolmes CSS,
but omit the editors and comparison controls. They do not show an unsaved parent
draft. Close the control to unload frames; refresh after publishing. If the site
blocks frames, use **Open preview**. Responsive frames retain the selected preview theme.

**Compare themes** is available in both the public gallery and **Admin styles**.
Choose two themes, one element section and an equal 360/768/1280 CSS-pixel width.
The independently styled panes scroll to the first example and stack on narrow
screens. They show published styles, suppress editor/comparison controls, and
unload when closed. Frontend choices respect the visitor theme allowlist; admin
choices use the core's validated GUI catalog and remain behind module-admin access.
Admin theme links also preview one theme without changing the configured GUI.

Verification: `php tests/theme-preview.php`, `php tests/admin-theme-loader.php /path/to/htdocs`
and `node tests/comparison-live.cjs` (set `XSTYLES_TEST_URL`). Set `XSTYLES_BROWSER=firefox`
and `PLAYWRIGHT_BROWSERS_PATH` for Firefox. The live check uses a fresh anonymous
profile and verifies that comparison leaves its normal visitor theme unchanged.

XTF themes using `data-theme` are supported alongside Bootstrap's `data-bs-theme`.
Direction preview still does not load a real RTL language's styles. New browser
tools do not depend on the XOOPS bootstrap. See [the migration notes](docs/xoops4-migration.md)
for tested package contracts and the remaining work for a native XOOPS 4 port.

Additional checks: `php tests/preview.php`, `node tests/bootstrap5.cjs`, and
`node tests/gallery-browser.cjs`. Browser tests use an existing Playwright install
(`PLAYWRIGHT_MODULE`, or a locally installed `playwright` package) and Chrome
(`CHROME_PATH`). `PHP_BINARY` selects PHP for browser fixtures; otherwise `php`
must be on PATH. `XSTYLES_CORE` selects the core
used by the isolated Smarty browser fixture harness; it never logs into a site.
For the real theme shell and inherited visitor theme, run `node tests/gallery-live.cjs`
with `XSTYLES_TEST_URL` set to the gallery URL. This uses a fresh anonymous browser
context and performs no form submissions or settings writes.

Addendum checks: `php tests/markup.php`, `php tests/theme-specimens.php`,
`node tests/mode-preview.cjs` and `node tests/mode-live.cjs` (the latter requires
`XSTYLES_TEST_URL`). Source distributions include CLI tests; the release ZIP
omits tests and development notes using `.gitattributes` export exclusions.

The installed xbootstrap5 and mytheme6 themes each include navigation, card and
content examples. The [tutorial](docs/TUTORIAL.md) explains how to explore them,
compare themes and use the gallery in a theme-design workflow.

## Qodana inspections

The optional Qodana workflow runs alongside existing quality checks. Add this
repository's `QODANA_TOKEN` secret to enable it; runs without the token print a
skip notice. See [Qodana setup and scope](docs/QODANA.md). No module-local
development vendor directory is required.
