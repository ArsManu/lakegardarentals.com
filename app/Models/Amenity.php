<?php

namespace App\Models;

use App\Models\Concerns\HasStoredTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasFactory;
    use HasStoredTranslations;

    protected $fillable = [
        'name',
        'slug',
        'icon_key',
        'sort_order',
        'translations',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'translations' => 'array',
        ];
    }

    protected function translatableFieldNames(): array
    {
        return [
            'name',
        ];
    }

    public function apartments(): BelongsToMany
    {
        return $this->belongsToMany(Apartment::class, 'apartment_amenity')->withTimestamps();
    }
}
