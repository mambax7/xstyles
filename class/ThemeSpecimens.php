<?php

declare(strict_types=1);

namespace XoopsModules\Xstyles;

/** xStyles-owned, framework-independent descriptor; never an executable provider or a template URL. */
final class ThemeSpecimens
{
    public static function load(string $themeDirectory): array
    {
        $result = ['samples' => [], 'errors' => [], 'modeAttributes' => ['data-bs-theme', 'data-theme', 'data-mt-mode']];
        $root = realpath($themeDirectory);
        if ($root === false || ! file_exists($root . '/modules/xstyles/specimens.json')) {
            return $result;
        }
        $root = str_replace('\\', '/', $root) . '/';
        $manifest = self::localFile($root, 'modules/xstyles/specimens.json');

        try {
            $json = $manifest === null ? false : file_get_contents($manifest, false, null, 0, 65537);
            if ($json === false || strlen($json) > 65536) {
                throw new \RuntimeException('unreadable-or-too-large');
            }
            $data = json_decode($json, true, 8, JSON_THROW_ON_ERROR);
            if (! is_array($data) || ($data['version'] ?? null) !== 1 || ! is_array($data['specimens'] ?? null)
                || ! array_is_list($data['specimens']) || count($data['specimens']) > 24) {
                throw new \RuntimeException('invalid-descriptor');
            }
            if (isset($data['modeAttributes'])) {
                $attributes = $data['modeAttributes'];
                if (! is_array($attributes) || ! array_is_list($attributes) || count($attributes) < 1 || count($attributes) > 4) {
                    throw new \RuntimeException('invalid-mode-attributes');
                }
                foreach ($attributes as $attribute) {
                    if (! is_string($attribute) || ! preg_match('/^data-[a-z][a-z0-9-]{0,47}$/D', $attribute)) {
                        throw new \RuntimeException('invalid-mode-attributes');
                    }
                }
                $result['modeAttributes'] = array_values(array_unique($attributes));
            }
        } catch (\Throwable) {
            $result['errors'][] = 'specimens.json: invalid, unreadable or unsupported descriptor';

            return $result;
        }
        $ids = [];
        foreach ($data['specimens'] as $number => $sample) {
            $error = 'specimens.json: entry ' . ($number + 1);
            if (! is_array($sample) || ! is_string($sample['id'] ?? null) || ! preg_match('/^[a-z][a-z0-9-]{0,47}$/D', $sample['id'])
                || isset($ids[$sample['id']]) || ! is_string($sample['title'] ?? null) || trim($sample['title']) === '' || strlen($sample['title']) > 200
                || ! is_string($sample['selector'] ?? null) || strlen($sample['selector']) > 300
                || ! is_string($sample['template'] ?? null) || strlen($sample['template']) > 200
                || ! preg_match('~^specimens/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_-]+\.tpl$~D', $sample['template'])) {
                $result['errors'][] = $error . ': invalid metadata or duplicate ID';

                continue;
            }
            $path = self::localFile($root, 'modules/xstyles/' . $sample['template']);
            if ($path === null || filesize($path) > 131072) {
                $result['errors'][] = $error . ': template missing, outside theme or too large';

                continue;
            }
            $ids[$sample['id']] = true;
            $result['samples'][] = array_intersect_key($sample, array_flip(['id', 'title', 'selector', 'template'])) + ['path' => $path];
        }

        return $result;
    }

    private static function localFile(string $root, string $relative): ?string
    {
        $path = realpath($root . $relative);
        if ($path === false) {
            return null;
        }
        $path = str_replace('\\', '/', $path);

        return str_starts_with($path, $root) && is_file($path) && is_readable($path) ? $path : null;
    }
}
