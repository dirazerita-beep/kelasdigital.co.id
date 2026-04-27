<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'order_index',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(ProductLesson::class, 'section_id')->orderBy('order_index');
    }
}
