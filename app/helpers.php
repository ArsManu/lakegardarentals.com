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
