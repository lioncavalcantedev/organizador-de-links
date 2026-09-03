<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'url',
    'image_url',
    'category',
    'category_variant',
    'position',
])]
class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    public function getImageUrlAttribute(?string $value): ?string
    {
        if (! $value || filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
