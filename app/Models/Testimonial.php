<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'rating',
        'review',
        'treatment_received',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'rating'     => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope for published testimonials only.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for ordered testimonials.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }
}
