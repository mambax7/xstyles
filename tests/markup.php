<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
define('XOOPS_ROOT_PATH', dirname(__DIR__));
require __DIR__ . '/../class/Fixtures.php';
$inert = new ReflectionMethod(XoopsModules\Xstyles\Fixtures::class, 'inertTemplate');
$html = <<<'HTML'
<!-- id="comment" --><p>id="plain" href="text"</p>
<form action="login.php"><label for="field">Example</label><input id="field" name=field data-id="keep" data-name="keep" data-for="keep" title='id="inside" href="inside"' aria-labelledby="field other">
<a href="page.php" data-href="keep">Link</a><button formaction=login.php onclick="go()">Try</button></form>
<script>const text = 'id="script" href="script"';</script>
<textarea id="area" title="greater > than">id="inside-textarea"</textarea>
HTML;
$result = $inert->invoke(null, $html, 'test', 'https://example.invalid/modules/xstyles');
foreach (['data-id="keep"', 'data-name="keep"', 'data-for="keep"', 'data-href="keep"', "title='id=\"inside\" href=\"inside\"'", '<p>id="plain" href="text"</p>', '<!-- id="comment" -->', 'id="script" href="script"', 'id="xs-test-field"', 'for="xs-test-field"', 'aria-labelledby="xs-test-field xs-test-other"', 'href="#xs-test"'] as $expected) {
    if (! str_contains($result, $expected)) {
        throw new RuntimeException('Attribute rewrite lost: ' . $expected);
    }
}
if (str_contains($result, 'login.php') || str_contains($result, 'onclick')) {
    throw new RuntimeException('Action survived');
}
if (! str_contains($result, 'id="xs-test-area" title="greater > than">id="inside-textarea"')) {
    throw new RuntimeException('Raw-text opening attributes or contents changed');
}
require_once __DIR__ . '/../class/SpecimenMarkup.php';
$gap = '<label for="xs_fixture_checkbox">Group</label>';
if (XoopsModules\Xstyles\SpecimenMarkup::missingLabelTargets($gap) !== ['xs_fixture_checkbox']
    || XoopsModules\Xstyles\SpecimenMarkup::missingLabelTargets($gap . '<input id="xs_fixture_checkbox">') !== []) {
    throw new RuntimeException('Observed renderer warning must disappear after markup is corrected');
}
echo "Attribute rewriting: quoted/unquoted, data attributes, comments and text passed.\n";
$custom = $inert->invoke(null, '<form id="demo"></form><input id="field" list="choices" form="demo"><datalist id="choices"></datalist><output for="field other"></output><pre id="source">Example</pre>', 'theme-card', 'https://example.invalid/modules/xstyles');
foreach (['id="xs-theme-card_source"', 'list="xs-theme-card_choices"', 'form="xs-theme-card_demo"', 'for="xs-theme-card_field xs-theme-card_other"'] as $expected) {
    if (! str_contains($custom, $expected)) {
        throw new RuntimeException('Theme reference/namespace broken: ' . $expected);
    }
}
foreach (['title', 'xmp', 'iframe', 'noembed', 'noframes', 'noscript'] as $element) {
    $raw = '<' . $element . '><b id="literal">id="text"</b></' . $element . '>';
    if ($inert->invoke(null, $raw, 'theme-card', 'https://example.invalid/modules/xstyles') !== $raw) {
        throw new RuntimeException('Raw HTML text changed: ' . $element);
    }
}
