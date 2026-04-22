<?php

use App\Support\LocalizedUrl;

if (! function_exists('localized_route')) {
    /**
     * @param  array<string, mixed>  $parameters
     */
    function localized_route(string $name, array $parameters = [], ?string $targetLocale = null, bool $absolute = true): string
    {
        return LocalizedUrl::route($name, $parameters, $targetLocale, $absolute);
    }
}

if (! function_exists('localized_alternates')) {
    /**
     * @return array<string, string>
     */
    function localized_alternates(): array
    {
        return LocalizedUrl::alternatesForRequest();
    }
}

if (! function_exists('localized_route_is')) {
    function localized_route_is(string $name): bool
    {
        return request()->routeIs($name, 'locale.'.$name);
    }
}

if (! function_exists('localized_hreflang_urls')) {
    /**
     * @return array<string, string>
     */
    function localized_hreflang_urls(): array
    {
        $a = localized_alternates();
        $def = (string) config('locales.default', 'en');
        $defaultUrl = $a[$def] ?? $a['en'] ?? url('/');

        return [
            'x-default' => $defaultUrl,
            'en' => $a['en'] ?? url('/'),
            'de' => $a['de'] ?? url('/de'),
            'it' => $a['it'] ?? url('/it'),
        ];
    }
}

if (! function_exists('trans_page_string')) {
    /**
     * Translate a CMS value when it is plain text, using the English string as the JSON key.
     * If the value contains HTML (e.g. Quill), it is left unchanged so DB locale merges / OpenAI
     * translations are used instead.
     */
    function trans_page_string(?string $value, string $fallback = ''): string
    {
        $s = (is_string($value) && trim($value) !== '') ? trim($value) : $fallback;
        if ($s === '') {
            return '';
        }
        if (strip_tags($s) !== $s) {
            return (is_string($value) && trim($value) !== '') ? (string) $value : $fallback;
        }

        return __($s);
    }
}

if (! function_exists('localized_route_is_any')) {
    /**
     * @param  list<string>  $names
     */
    function localized_route_is_any(array $names): bool
    {
        $expanded = [];
        foreach ($names as $n) {
            $expanded[] = $n;
            $expanded[] = 'locale.'.$n;
        }

        return request()->routeIs(...$expanded);
    }
}
