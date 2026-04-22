<?php

namespace App\Support;

/**
 * Strips tags and decodes common HTML entities (&nbsp; &mdash; &amp; …) for plain output.
 * Use for cards and meta: Blade {{ }} would otherwise show literal &nbsp; text.
 */
final class PlainText
{
    public static function fromHtml(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
