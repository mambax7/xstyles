# xStyles 1.5.0 Beta 3

**A living style guide for XOOPS themes.** Preview how the current theme styles
common page elements, compare alternatives, and inspect real rendered HTML.

This beta is intended for testing on a development or staging site.

## Highlights

- Browse typography, content, tables, forms, comments, blocks and menus using
  the selected theme's own styles and the XOOPS form renderer.
- Preview administration themes and compare two themes side by side without
  changing the site's active administration theme.
- Check responsive layouts at 360, 768 and 1280 CSS pixels, search examples,
  and copy their rendered HTML.
- Explore optional Bootstrap 5 and Bootstrap 6 Alpha examples using assets
  supplied by the theme; xStyles does not install either framework.
- With compatible cssHolmes installed, authorized users can **Inspect example**
  or **Edit styles**, compare published and draft styles, and preview the draft
  at multiple widths. The modules remain independently usable.

Beta 3 adds the cssHolmes gallery connection and its regression checks, plus
release packaging and shared-tool compatibility updates. Earlier 1.5 beta work
provides the shared frontend/admin workspace, theme comparisons and tutorial.

## Requirements and boundaries

- **XOOPS 2.7+**, **PHP 8.2+**, XMF and the core form-renderer API.
- Smarty 4 or 5 through the corresponding XOOPS core.
- The optional **cssHolmes 3.3.0 Alpha 1** designer requires **PHP 8.4+** and its
  own permissions. Its preload supplies the designer controls and scripts.
- Frontend switching to a different theme requires the newer core request-only
  module-theme override available in the tested XOOPS 2.8 tree. XOOPS 2.7 still
  supports viewing the current frontend theme and administration-theme previews.
- Native XOOPS 4 installation is not certified by this release.

## Install or update

1. Back up the site files and database. Test upgrades on a copy first.
2. Extract **xstyles-v1.5.0-beta.3.zip** into `htdocs/modules/`, retaining the
   `xstyles` directory name. Use this installable asset rather than GitHub's
   automatically generated source archives.
3. In **System → Modules**, install xStyles or run **Update** for an existing
   installation so its registered templates are refreshed.
4. Open **Go to module** for frontend examples or **Admin styles** for the
   administration gallery. Read the included `docs/TUTORIAL.md`.
5. To use the optional designer, install/update compatible cssHolmes separately
   and review its Overlay/Edit, Workbench and Export permissions.

xStyles creates no content tables, sample records or upload folders. Its example
forms do not save content or upload files. cssHolmes **Save to theme** is a
separate publishing action and can affect visitors.

The installable ZIP excludes tests, development tools, private `docs/00` notes,
and vendor dependencies. No module-local Composer installation is required.

## Verification

The PHP 8.2–8.5 CI matrix and frontend/admin browser regressions passed during
release preparation. Isolated checks cover XOOPS 2.7 / Smarty 4 and XOOPS 2.8 /
Smarty 5. The ZIP update and designer save/reload/restore workflow also passed
on a disposable XOOPS 2.7.4-RC2 / PHP 8.4 site. This does not replace testing
your own theme and modules; a fresh Beta 3 installation was not repeated.
