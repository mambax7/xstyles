<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit('CLI only');
}
// Reuse the real-core harness; all template caches live in its temporary directory.
ob_start();
require __DIR__ . '/fixtures.php';
ob_end_clean();
$samples = array_merge($samples, $themeOutput['samples']);
foreach ($samples as &$sample) {
    $sample['url'] = '#' . $sample['id'];
}
unset($sample);
$frame->assign([
    'xs_samples' => $samples, 'xs_form_renderer' => 'theme', 'xs_preview_frame' => false,
    'xs_preview_url' => '/modules/xstyles/index.php?section=all&mode=theme&direction=theme&zones=off&renderer=theme&preview_frame=1',
]);
$parent = $frame->fetch('db:xstyles_index.tpl');
$frame->assign('xs_preview_frame', true);
echo json_encode(['parent' => $parent, 'child' => $frame->fetch('db:xstyles_index.tpl')], JSON_THROW_ON_ERROR);
