<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::query()->where('slug', 'lake-garda')->first();
        if ($page === null || ! is_array($page->blocks)) {
            return;
        }

        $b = $page->blocks;
        if (! empty($b['flex_blocks'])) {
            return;
        }

        $flex = [];

        $intro = trim((string) ($b['intro'] ?? ''));
        if ($intro !== '') {
            $flex[] = ['type' => 'rich_text', 'html' => $intro];
        }

        $why = trim((string) ($b['why_garda'] ?? ''));
        if ($why !== '') {
            $flex[] = ['type' => 'rich_text', 'html' => '<h2>Why stay in Garda</h2>'.$why];
        }

        $attr = trim((string) ($b['attractions'] ?? ''));
        if ($attr !== '') {
            $flex[] = ['type' => 'rich_text', 'html' => '<h2>Beaches, old town & activities</h2>'.$attr];
        }

        $couples = trim((string) ($b['for_couples'] ?? ''));
        $families = trim((string) ($b['for_families'] ?? ''));
        if ($couples !== '' || $families !== '') {
            $html = '';
            if ($couples !== '') {
                $html .= '<h2>Ideal for couples</h2>'.$couples;
            }
            if ($families !== '') {
                $html .= '<h2>Ideal for families</h2>'.$families;
            }
            $flex[] = ['type' => 'rich_text', 'html' => $html];
        }

        if ($flex === []) {
            return;
        }

        foreach (['intro', 'why_garda', 'attractions', 'for_couples', 'for_families'] as $key) {
            unset($b[$key]);
        }
        $b['flex_blocks'] = $flex;

        $page->update(['blocks' => $b]);
    }
};
