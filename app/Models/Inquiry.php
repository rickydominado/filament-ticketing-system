<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Inquiry extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'title',
        'content',
        'category_id',
        'status',
        'severity',
        'has_notification',
    ];

    protected $casts = [
        'status' => \App\Enums\Inquiry\Status::class,
        'severity' => \App\Enums\Inquiry\Severity::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }
}
