# Changelog

## 1.5.0 Beta 3 - 2026-10-26

- Add permission-aware cssHolmes Inspect/Edit actions to frontend examples and a preview URL for private draft comparisons and responsive previews.
- Verify rendered designer controls and keep the shared CI Rector version compatible with the XOOPS rules.
- Prepare a clean prerelease package with tag-specific release notes and explicit prerelease classification.

## 1.5.0 Beta 2 - 2026-10-25

- Share workspace navigation, rounded controls and rendered-HTML panels across frontend and admin galleries.
- Preview and compare admin themes without activating them; preserve comparison controls and scroll position. Frontend theme switching requires the newer core request-only override.
- Add Bootstrap 6 Alpha and isolated theme-supplied examples using the selected theme's assets.
- Restore the standard Home dashboard, admin tab order and Help layout; clarify user-facing terminology.
- Preserve data attributes and quoted preview markup, stabilize colour previews, and report observed core form-label defects.

- Add optional Qodana PHP inspections, token-gated CI and a setup guide without adding local development dependencies.

- Use shared XOOPS development tools instead of a module-local Composer manifest and vendor tree; retain module-specific configurations and install CI tools outside the checkout.
- Run isolated core integration checks on XOOPS 2.7.4 / Smarty 4 as well as XOOPS 2.8 / Smarty 5; document the frontend theme-preview capability difference.
- Load browser-test module assets from the module under test when using a separate core checkout.

- Adopt the XOOPS 2.7 module-devops quality gate, PHP 8.2–8.5 CI, Dependabot, optional SonarCloud and QA-gated release archives for XoopsModules27x/xstyles.
- Run existing standalone and optional core integration checks through PHPUnit.
- Remove unused legacy icons, duplicate root images, the extras stylesheet, prototype, placeholder documents and obsolete Code Climate configuration. Custom templates referencing root images must use `assets/images/`.
- Exclude private `docs/00/` notes and development files from distribution; include the illustrated tutorial.

## 1.5.0 Beta 1 - 2026-10-25

- Added advanced core forms, scoped renderer comparison, tabbed forms, login/search blocks, pagination variants, and comment sequences.
- Added an optional Bootstrap 5 component gallery using only the active theme's assets.
- Added published-state responsive previews at actual 360, 768, and 1280 CSS-pixel viewports.
- Recognize XTF `data-theme` previews; verify fixtures against local XOOPS 2.8 and 4.0 classes.
- Added browser checks and documented the remaining native XOOPS 4 migration boundaries.

## 1.4.0 Beta 1 - 2026-10-20

- Added specimen filtering, keyboard reset and permalink recovery.
- Added a comment preview container for grid gutters.
- Fixed main-menu sublink metadata and code sanitizer preprocessing; added regression checks.

- Replaced the legacy page with registered, theme-overridable Smarty specimens.
- Added section navigation, contextual theme/locale/renderer information,
  shareable previews and rendered HTML with copy controls.
- Added real core forms, pagination, sanitizer output, comments and menus.
- Added request-only block-zone previews and permission-aware cssHolmes links.
- Kept site branding and scoped the gallery's responsive interface styles.
- Removed unused uploads, content-module utilities and generated test placeholders;
  lifecycle callbacks preserve historical files and permissions.
- Added executable gallery, fixture and lifecycle regression checks.
- Aligned requirements with XOOPS 2.7+ / PHP 8.2+; verified locally on 2.8.

## 1.3.0 - 2026-04-25

- Added a direct-access guard before the manifest autoloader bootstrap.
- Escaped the help header template URL/title/alt output.
- Switched the manifest name and description to `_MI_XSTYLES_*` language
  constants.
- Declared the module as schema-free with an empty table list in
  `xoops_version.php`.
- Clarified the module purpose, PHP requirement, compatibility marker, and
  `extras/extras.css` role in the README.
- Hardened Common database helper SQL assembly with identifier validation,
  value quoting, and two-part result guards.
