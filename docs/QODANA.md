# Qodana inspections

Qodana adds PhpStorm-based PHP, JavaScript and TypeScript inspections alongside
the existing tests and static analysis. The module uses Qodana PHP 2026.2 and the
`qodana.recommended` profile. `qodana.yaml` sets the module's supported PHP level.

## Enable analysis

1. Create a Qodana Cloud project for this repository, or connect it through the
   Qodana GitHub App. Qodana PHP requires an appropriate Qodana license.
2. Save that project's token as the repository Actions secret `QODANA_TOKEN`.
   Use a separate project/token for each module; do not reuse the core project's
   token for every repository or commit tokens to configuration files.
3. Run **Actions > Qodana > Run workflow** after the workflow reaches the default
   branch. It also runs for pushes to `master`, `main` and `releases/*`, and PRs.
4. Review the Qodana Cloud report and the `qodana-report` workflow artifact.

When the secret is unavailable, including ordinary fork and Dependabot PRs,
the workflow prints a skip notice. Do not use `pull_request_target` to expose
tokens to untrusted code. PR comments and automatic code fixes are disabled.

## Scope and dependencies

The scan excludes vendor packages, generated output, uploads and private
`docs/00` notes. It does not create a Composer manifest or install module-local
development tools. Keep normal tests and quality tools in the shared
`xoops_lib/vendor` installation.

A standalone module checkout may lack XOOPS core classes and external libraries.
Review unresolved-symbol findings in that context; do not disable all such
inspections or generate a blanket baseline. For full dependency analysis, use a
checkout containing the appropriate core/library sources, or add a reviewed
`bootstrap` command for the module's unique runtime dependencies. The baseline
deliberately does not install every module's Composer development requirements.

## Maintenance

Keep the Qodana action and image on matching release lines. GitHub Actions
Dependabot entries, where configured, track the pinned action. Review linter
image updates separately. Change `php.version` when the module's supported PHP
level changes. Generated `.qodana/` files are ignored and excluded from releases.

No new issue-count threshold or baseline is imposed by this configuration.
Agree on a gate after reviewing the first report; existing tests remain required.

See JetBrains' [GitHub integration](https://www.jetbrains.com/help/qodana/github.html)
and [PHP configuration](https://www.jetbrains.com/help/qodana/php.html).
