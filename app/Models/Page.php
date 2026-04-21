<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'blocks',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
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
