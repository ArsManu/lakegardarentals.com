<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'full_description',
        'ideal_for',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'size_m2',
        'price_from',
        'location_text',
        'address',
        'check_in_out_note',
        'featured_image',
        'availability_note',
        'is_active',
        'sort_order',
        'external_listing_url',
        'license_cir',
        'license_cin',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
    ];

    protected function casts(): array
    {
        return [
            'price_from' => 'decimal:2',
            'is_active' => 'boolean',
            'max_guests' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'size_m2' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(ApartmentImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(ApartmentSeason::class)->orderBy('sort_order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'apartment_amenity')->withTimestamps();
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function coverImagePath(): ?string
    {
        if ($this->featured_image) {
            return $this->featured_image;
        }

        $cover = $this->images()->where('is_cover', true)->first();

        return $cover?->path ?? $this->images()->first()?->path;
    }
}
