<?php

namespace App\Models;

use App\Models\Concerns\HasStoredTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    use HasStoredTranslations;

    protected $fillable = [
        'slug',
        'title',
        'blocks',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'translations' => 'array',
        ];
    }

    protected function translatableFieldNames(): array
    {
        return [
            'title',
            'blocks',
            'meta_title',
            'meta_description',
            'og_title',
            'og_description',
        ];
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::query()->where('slug', $slug)->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
