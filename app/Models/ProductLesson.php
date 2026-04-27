<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'title',
        'type',
        'youtube_id',
        'gdrive_file_id',
        'content',
        'duration_minutes',
        'order_index',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ProductSection::class, 'section_id');
    }
}
