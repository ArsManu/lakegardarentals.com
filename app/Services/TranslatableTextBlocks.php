<?php

namespace App\Services;

/**
 * Recursively collect and re-apply translatable string leaves in page block arrays.
 */
final class TranslatableTextBlocks
{
    /** @var list<string> */
    private const TEXT_KEYS = [
        'eyebrow',
        'title',
        'subtitle',
        'hero_title',
        'hero_subtitle',
        'reassurance',
        'image_alt',
        'primary_cta_label',
        'secondary_cta_label',
        'text',
        'heading',
        'body_html',
        'html',
        'caption',
        'left_alt',
        'right_alt',
        'cta_title',
        'cta_text',
        'intro',
        'why_garda',
        'attractions',
        'for_couples',
        'for_families',
    ];

    /** @var list<string> */
    private const HTML_LIKE_KEYS = [
        'html',
        'body_html',
        'intro',
        'why_garda',
        'attractions',
        'for_couples',
        'for_families',
        'hero_subtitle',
        'reassurance',
    ];

    /**
     * @return list<array{value: string, is_html: bool}>
     */
    public static function extractSegments(array $data): array
    {
        $out = [];
        self::walkExtract($data, $out);

        return $out;
    }

    /**
     * @param  list<string>  $translated  In the same order as {@see extractSegments()}
     * @return array<array-key, mixed>
     */
    public static function apply(array $data, array $translated): array
    {
        $data = (array) json_decode(json_encode($data, flags: JSON_THROW_ON_ERROR), true, flags: JSON_THROW_ON_ERROR);
        $i = 0;
        self::walkApply($data, $translated, $i);

        return $data;
    }

    private static function isTranslatableStringKey(string $k): bool
    {
        return in_array($k, self::TEXT_KEYS, true);
    }

    private static function isHtmlValue(string $key, string $value): bool
    {
        if (in_array($key, self::HTML_LIKE_KEYS, true)) {
            return true;
        }
        if (str_ends_with($key, '_html')) {
            return true;
        }

        // Quill and other WYSIWYGs often use keys like "subtitle" or "title" for full HTML. Plain-mode
        // translation strips or mangles tags, leaving bare closing tags visible in the UI.
        return $value !== strip_tags($value);
    }

    /**
     * @param  list<array{value: string, is_html: bool}>  $out
     */
    private static function walkExtract(mixed $node, array &$out): void
    {
        if (is_array($node)) {
            foreach ($node as $k => $v) {
                if (! is_string($k)) {
                    if (is_array($v)) {
                        self::walkExtract($v, $out);
                    }

                    continue;
                }
                if (self::isTranslatableStringKey($k) && is_string($v) && $v !== '') {
                    $out[] = [
                        'value' => $v,
                        'is_html' => self::isHtmlValue($k, $v),
                    ];
                } elseif (is_array($v)) {
                    self::walkExtract($v, $out);
                }
            }
        }
    }

    private static function walkApply(mixed &$node, array $translated, int &$i): void
    {
        if (! is_array($node)) {
            return;
        }
        foreach ($node as $k => &$v) {
            if (! is_string($k)) {
                if (is_array($v)) {
                    self::walkApply($v, $translated, $i);
                }

                continue;
            }
            if (self::isTranslatableStringKey($k) && is_string($v) && $v !== '' && $i < count($translated)) {
                $v = $translated[$i];
                $i++;
            } elseif (is_array($v)) {
                self::walkApply($v, $translated, $i);
            }
        }
    }
}
