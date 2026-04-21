<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'apartments'],
            [
                'title' => 'Apartments listing',
                'meta_title' => 'Lake Garda apartments in Garda',
                'meta_description' => 'Compare our two holiday apartments in Garda on Lake Garda. Direct booking inquiries, clear pricing, and fast host responses.',
                'canonical_url' => url('/apartments'),
                'blocks' => [
                    'hero_title' => 'Our apartments',
                    'hero_subtitle' => 'Two thoughtfully prepared rentals in Garda—ideal for couples and families exploring Lake Garda.',
                ],
            ]
        );
    }

    public function down(): void
    {
        Page::query()->where('slug', 'apartments')->delete();
    }
};
