<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}

ob_start();
require __DIR__ . '/fixtures.php';
ob_end_clean();
$rendered = [];
foreach (['xbootstrap5', 'mytheme6'] as $theme) {
    $definitions = XoopsModules\Xstyles\ThemeSpecimens::load(XOOPS_ROOT_PATH . '/themes/' . $theme);
    check($definitions['errors'] === [], $theme . ': invalid descriptor');
    check(count($definitions['samples']) === 3, $theme . ': expected navigation, cards and content');
    $output = XoopsModules\Xstyles\Fixtures::theme($definitions['samples'], '/modules/xstyles', '/themes/' . $theme);
    check($output['errors'] === [] && count($output['samples']) === 3, $theme . ': render failure');
    $html = implode('', array_column($output['samples'], 'html'));
    $dom = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($dom);
    $ids = [];
    foreach ($xpath->query('//*[@id]') as $node) {
        $ids[] = $node->getAttribute('id');
    }
    check(count($ids) === count(array_unique($ids)), $theme . ': duplicate IDs');
    foreach ($xpath->query('//*[@aria-labelledby]') as $node) {
        foreach (explode(' ', $node->getAttribute('aria-labelledby')) as $id) {
            check(in_array($id, $ids, true), $theme . ': broken heading reference');
        }
    }
    check($xpath->query('//script|//link|//iframe|//form')->length === 0, $theme . ': unexpected assets or actions');
    check(! str_contains($html, '<{'), $theme . ': uncompiled Smarty');
    foreach ($xpath->query('//a[@href]') as $link) {
        check(str_starts_with($link->getAttribute('href'), '#'), $theme . ': active navigation');
    }
    $samples = $output['samples'];
    foreach ($samples as &$sample) {
        $sample['url'] = '#' . $sample['id'];
    }
    unset($sample);
    $frame->assign([
        'xs_samples' => $samples, 'xs_section' => 'theme', 'xs_theme' => $theme,
        'xs_preview_frame' => false, 'xs_admin' => false, 'xs_form_renderer' => 'theme',
        'xs_preview_url' => '/' . $theme . '/index.php?preview_frame=1',
    ]);
    $parent = $frame->fetch('db:xstyles_index.tpl');
    $frame->assign('xs_preview_frame', true);
    $rendered[$theme] = ['parent' => $parent, 'child' => $frame->fetch('db:xstyles_index.tpl')];
}
echo in_array('--json', $argv, true)
    ? json_encode($rendered, JSON_THROW_ON_ERROR)
    : "Installed specimens: both themes render navigation, cards and content with unique references and inert actions.\n";
