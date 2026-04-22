<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromPrefix
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale', '');

        if (in_array($locale, config('locales.prefixed', []), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
