![alt XOOPS CMS](https://xoops.org/images/logoXoopsPhp81.png)
# Contributing to [XOOPS CMS](https://xoops.org)

[![XOOPS CMS Module](https://img.shields.io/badge/XOOPS%20CMS-Module-blue.svg)](https://xoops.org)
[![Software License](https://img.shields.io/badge/license-GPL-brightgreen.svg?style=flat)](https://www.gnu.org/licenses/gpl-2.0.html)

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [GitHub](https://github.com/XoopsModules27x/xstyles).

## Local checks

Install testing and quality tools once through the development manifest in
`xoops_lib`. Run the shared PHP-CS-Fixer, PHPStan, Rector and PHPUnit executables
from the module directory using the commands in the README. Keep module-specific
configuration here; do not create a duplicate development vendor directory.
Set `XSTYLES_CORE` to a compatible XOOPS document root to include the optional
core integration checks. See the README for browser checks and compatibility limits.

Reduce the initial PHPStan baseline as runtime declarations and types improve;
do not expand it to suppress new problems. Keep private notes in ignored `docs/00/`.
Use Conventional Commits, such as `fix(gallery): retain comparison controls`, and
describe the behavior change, compatibility impact, linked issue and checks in PRs.

## Pull Requests

- Keep admin tabs in this order: Home, feature tabs, About, Help. About is always immediately before the core-provided Help tab. Use the standard Go to module link instead of adding a duplicate frontend link on Home.
- Preserve the standard XOOPS Help layout: `h1.head` with the module/home link, uppercase `h4.odd` section bars, and `even` content panels. Keep tutorial highlights within the standard sections instead of applying the gallery workspace design to Help.
- Check visual consistency in both frontend and admin galleries: use the shared workspace styles, spacing, rounded controls and source panels. Scope UI styles to gallery controls; examples must retain the selected theme's real styling. Verify narrow screens, keyboard focus and comparison scrolling before delivery.

- **[PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)** - Run the shared PHP-CS-Fixer with this module's `.php-cs-fixer.dist.php` configuration.
- **Add tests!** - We encourage providing tests for your contributions.
- **Document any change in behavior** - Make sure the `/docs/changelog.txt` and any other relevant documentation are up-to-date.
- **Consider our release cycle** - We try to follow [Semantic Versioning v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.
- **Create feature branches** - Don't ask us to pull from your master branch.
- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.
- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

Happy coding, and **_May the Source be with You_**!
