<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $urls[] = ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'];

        foreach (['lake-garda', 'contact'] as $slug) {
            if (Page::query()->where('slug', $slug)->exists()) {
                $urls[] = [
                    'loc' => url('/'.$slug),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }
        }

        $urls[] = ['loc' => route('apartments.index'), 'changefreq' => 'weekly', 'priority' => '0.9'];

        Apartment::query()->where('is_active', true)->orderBy('sort_order')->each(function (Apartment $a) use (&$urls): void {
            $urls[] = [
                'loc' => route('apartments.show', $a),
                'changefreq' => 'weekly',
                'priority' => '0.85',
            ];
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $u) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($u['loc']).'</loc>'."\n";
            $xml .= '    <changefreq>'.$u['changefreq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$u['priority'].'</priority>'."\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
