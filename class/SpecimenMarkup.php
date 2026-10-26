<?php

declare(strict_types=1);

namespace XoopsModules\Xstyles;

/** Transform attributes of trusted theme markup without reserializing its HTML. Not an HTML sanitizer. */
final class SpecimenMarkup
{
    /** Report observed defects in the core form specimens, not guessed renderer versions. */
    public static function missingLabelTargets(string $html): array
    {
        $ids = [];
        $targets = [];
        self::attributes($html, static function (string $name, string $value) use (&$ids, &$targets): false {
            if ($name === 'id') {
                $ids[] = $value;
            } elseif ($name === 'for' && preg_match('/^xs_(fixture|advanced|tabs)_/', $value)) {
                $targets[] = $value;
            }

            return false;
        });

        return array_values(array_unique(array_diff($targets, $ids)));
    }

    public static function attributes(string $html, callable $replace, ?string $formAction = null): string
    {
        // Consume quoted values and raw-text elements whole, so their contents cannot look like attributes.
        return preg_replace_callback(
            '~<!--.*?-->|<(script|style|textarea|title|xmp|iframe|noembed|noframes|noscript)\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*>.*?</\1\s*>|<([a-z][a-z0-9:-]*)(\s+(?:"[^"]*"|\'[^\']*\'|[^\'">])*)?\s*/?>~is',
            static function (array $tag) use ($replace, $formAction): string {
                if (! empty($tag[1])) {
                    preg_match('~^<(?:"[^"]*"|\'[^\']*\'|[^\'">])*>~s', $tag[0], $opening);
                    $end = strlen($opening[0]);

                    return self::attributes(substr($tag[0], 0, $end), $replace) . substr($tag[0], $end);
                }
                if (empty($tag[2])) {
                    return $tag[0];
                }
                $result = preg_replace_callback(
                    '~\s+([^\s"\'<>/=]+)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?~',
                    static function (array $attribute) use ($replace): string {
                        $raw = $attribute[2] ?? '';
                        $value = $raw !== '' && ($raw[0] === '"' || $raw[0] === "'") ? substr($raw, 1, -1) : $raw;
                        $replacement = $replace(strtolower($attribute[1]), html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                        return $replacement === false ? $attribute[0]
                            : ($replacement === null ? '' : ' ' . $attribute[1] . '="' . htmlspecialchars($replacement, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"');
                    },
                    $tag[0]
                );
                if (strtolower($tag[2]) === 'form' && $formAction !== null) {
                    return substr_replace($result, ' data-xs-demo-form="true" action="' . htmlspecialchars($formAction, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"', 5, 0);
                }

                return $result;
            },
            $html
        );
    }
}
