<?php

namespace App\Models;

use App\Models\Concerns\HasStoredTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    use HasStoredTranslations;

    protected $fillable = [
        'author_name',
        'author_location',
        'quote',
        'rating',
        'sort_order',
        'is_published',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'translations' => 'array',
        ];
    }

    protected function translatableFieldNames(): array
    {
        return [
            'author_name',
            'author_location',
            'quote',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
