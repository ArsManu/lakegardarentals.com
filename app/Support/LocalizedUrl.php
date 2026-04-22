<?php

namespace App\Support;

use Illuminate\Http\Request;

class LocalizedUrl
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = [], ?string $targetLocale = null, bool $absolute = true): string
    {
        $targetLocale = $targetLocale ?? app()->getLocale();

        if (self::localeUsesPrefix($targetLocale)) {
            $prefixed = 'locale.'.$name;

            return route($prefixed, array_merge(['locale' => $targetLocale], $parameters), $absolute);
        }

        return route($name, $parameters, $absolute);
    }

    public static function isPrefixedRouteName(?string $name): bool
    {
        return is_string($name) && str_starts_with($name, 'locale.');
    }

    /**
     * @return array<string, string> locale => full URL
     */
    public static function alternatesForRequest(?string $baseRouteName = null, ?array $parameters = null): array
    {
        $route = request()->route();
        if ($baseRouteName === null && $route?->getName() === null) {
            return [
                'en' => url('/'),
                'de' => url('/de'),
                'it' => url('/it'),
            ];
        }
        $baseRouteName = $baseRouteName ?? (string) $route?->getName();
        $parameters = $parameters ?? ($route?->parameters() ?? []);
        $base = self::toBaseRouteName($baseRouteName, $parameters);

        $urls = [];
        foreach (config('locales.supported', ['en', 'de', 'it']) as $locale) {
            $params = $base['params'];
            $urls[$locale] = self::route($base['name'], $params, $locale);
        }

        return $urls;
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array{name: string, params: array<string, mixed>}
     */
    public static function toBaseRouteName(string $routeName, array $parameters): array
    {
        if (self::isPrefixedRouteName($routeName)) {
            $baseName = substr($routeName, strlen('locale.'));
            unset($parameters['locale']);

            return [
                'name' => $baseName,
                'params' => $parameters,
            ];
        }

        return [
            'name' => $routeName,
            'params' => $parameters,
        ];
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    public static function hreflangMap(array $overrides, ?Request $request = null): array
    {
        $request = $request ?? request();
        $route = $request->route();
        if ($route === null) {
            return $overrides;
        }

        $alts = self::alternatesForRequest();
        $defaultLocale = (string) config('locales.default', 'en');

        return [
            'x-default' => $alts[$defaultLocale] ?? $overrides['x-default'] ?? url('/'),
            'en' => $alts['en'] ?? $overrides['en'] ?? url('/'),
            'de' => $alts['de'] ?? $overrides['de'] ?? url('/de'),
            'it' => $alts['it'] ?? $overrides['it'] ?? url('/it'),
        ];
    }

    public static function localeUsesPrefix(string $locale): bool
    {
        return in_array($locale, config('locales.prefixed', []), true);
    }

    /**
     * Canonical for the current request's localized page, if route is a known public route.
     */
    public static function currentCanonicalUrl(): string
    {
        $route = request()->route();
        if ($route === null) {
            return url()->current();
        }

        $alts = self::alternatesForRequest();
        $currentLocale = app()->getLocale();

        return $alts[$currentLocale] ?? url()->current();
    }
}
