<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'badge',
        'content',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content'    => 'array',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope for visible sections only.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for ordered sections.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}
