<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'show_on_website',
        'is_featured',
        'icon',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'            => 'decimal:2',
            'duration_minutes' => 'integer',
            'is_active'        => 'boolean',
            'show_on_website'  => 'boolean',
            'is_featured'      => 'boolean',
            'sort_order'       => 'integer',
        ];
    }

    /**
     * Scope for services visible on the public website.
     */
    public function scopeVisibleOnWebsite(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->where('show_on_website', true)
                     ->orderBy('sort_order', 'asc')
                     ->orderBy('price', 'asc');
    }

    /**
     * Scope for featured services on landing page.
     */
    public function scopeFeaturedOnWebsite(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->where('show_on_website', true)
                     ->orderBy('is_featured', 'desc')
                     ->orderBy('sort_order', 'asc');
    }

    /**
     * Scope for ordered services.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Appointments for this service.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
