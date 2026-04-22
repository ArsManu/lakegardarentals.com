<?php

namespace App\Support;

/**
 * Detects broken HTML where closing tags outnumber opening tags (common when a
 * translation model drops opening tags). Browsers can show closings as visible text.
 * Use {@see toDisplayableHtml()} to fall back to safe plain text at render time.
 */
final class HtmlTranslationSanity
{
    /**
     * @var list<string>
     */
    private const INLINE_BLOCK_TAGS = ['p', 'span', 'div', 'a', 'em', 'i', 'strong', 'b', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    public static function isStructurallySane(string $html): bool
    {
        if ($html === '' || $html === strip_tags($html)) {
            return true;
        }
        foreach (self::INLINE_BLOCK_TAGS as $tag) {
            if (self::openTagCount($html, $tag) < self::closeTagCount($html, $tag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * For trusted CMS HTML: return as-is when the fragment is structurally sound.
     * When it is not (e.g. orphan closings from a bad LLM run), return a single safe paragraph
     * of plain text so the browser does not show visible closing tags.
     */
    public static function toDisplayableHtml(string $html): string
    {
        $html = (string) $html;
        if (trim($html) === '') {
            return '';
        }
        $html = self::stripLlmJsonEscapesFromHtml($html);
        if (self::isStructurallySane($html)) {
            return $html;
        }
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');
        $plain = trim(preg_replace('/\*+$/u', '', $plain) ?? $plain);
        $plain = trim($plain, " \t\n\r\0\x0B*");

        return $plain === '' ? '' : '<p>'.htmlspecialchars($plain, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</p>';
    }

    /**
     * LLMs sometimes insert JSON string escapes into HTML (e.g. {@code <\/p>}) which survive json_decode
     * as visible backslashes; the browser then shows tags as text. Normalize to real tag delimiters.
     */
    public static function stripLlmJsonEscapesFromHtml(string $html): string
    {
        if ($html === '' || ! str_contains($html, '\\')) {
            return $html;
        }
        // JSON-escaped closers like <\/span> become visible as text in the browser if not normalized.
        $html = str_replace('\/', '/', $html);
        $html = str_replace(['\\<', '\\>'], ['<', '>'], $html);

        return $html;
    }

    public static function openTagCount(string $html, string $name): int
    {
        $n = preg_quote($name, '/');
        $pat = "/<{$n}\\b(?=\\s|>)/i";

        return preg_match_all($pat, $html) ?: 0;
    }

    public static function closeTagCount(string $html, string $name): int
    {
        $n = preg_quote($name, '/');

        return preg_match_all("/<\\/{$n}>/i", $html) ?: 0;
    }
}
