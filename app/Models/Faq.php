<?php

namespace App\Models;

use App\Models\Concerns\HasStoredTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    use HasStoredTranslations;

    protected $fillable = [
        'page_slug',
        'question',
        'answer',
        'sort_order',
        'is_active',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'translations' => 'array',
        ];
    }

    protected function translatableFieldNames(): array
    {
        return [
            'question',
            'answer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPageSlug(Builder $query, string $pageSlug): Builder
    {
        return $query->where('page_slug', $pageSlug);
    }
}
