<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'email',
        'title',
        'content',
        'category_id',
        'status',
        'severity',
    ];

    protected $casts = [
        'status' => App\Enums\Inquiry\Status::class,
        'severity' => App\Enums\Inquiry\Severity::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
