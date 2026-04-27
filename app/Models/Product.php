<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'thumbnail',
        'type',
        'status',
        'commission_rate',
        'preview_youtube_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'commission_rate' => 'decimal:2',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ProductSection::class)->orderBy('order_index');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function buyers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_products')
            ->withPivot('order_id')
            ->withTimestamps();
    }
}
