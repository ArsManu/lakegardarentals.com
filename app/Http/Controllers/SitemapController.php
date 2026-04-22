<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Page;
use App\Support\LocalizedUrl;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];
        $locales = (array) config('locales.supported', ['en', 'de', 'it']);

        foreach ($locales as $loc) {
            $urls[] = $this->entry(LocalizedUrl::route('home', [], $loc), 'weekly', '1.0');

            if (Page::query()->where('slug', 'lake-garda')->exists()) {
                $urls[] = $this->entry(LocalizedUrl::route('lake-garda', [], $loc), 'monthly', '0.8');
            }
            if (Page::query()->where('slug', 'contact')->exists()) {
                $urls[] = $this->entry(LocalizedUrl::route('contact', [], $loc), 'monthly', '0.8');
            }

            $urls[] = $this->entry(LocalizedUrl::route('apartments.index', [], $loc), 'weekly', '0.9');
        }

        $aptRows = Apartment::query()->where('is_active', true)->orderBy('sort_order')->get();
        foreach ($aptRows as $a) {
            foreach ($locales as $loc) {
                $urls[] = $this->entry(LocalizedUrl::route('apartments.show', ['apartment' => $a], $loc), 'weekly', '0.85');
            }
        }

        $urls = $this->uniqueByLoc($urls);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $u) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($u['loc']).'</loc>'."\n";
            $xml .= '    <changefreq>'.$u['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$u['priority']."</priority>\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return array{loc: string, changefreq: string, priority: string}
     */
    private function entry(string $loc, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    /**
     * @param  list<array{loc: string, changefreq: string, priority: string}>  $urls
     * @return list<array{loc: string, changefreq: string, priority: string}>
     */
    private function uniqueByLoc(array $urls): array
    {
        $out = [];
        $seen = [];
        foreach ($urls as $u) {
            if (isset($seen[$u['loc']])) {
                continue;
            }
            $seen[$u['loc']] = true;
            $out[] = $u;
        }

        usort($out, fn ($a, $b) => strcmp($a['loc'], $b['loc']));

        return $out;
    }
}
