<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'template_type',
        'description',
        'content',
        'is_published',
        'kop_left_image',
        'kop_right_image',
        'signature_image',
        'layout_settings',
        'current_step',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(DocumentPart::class)->orderBy('order');
    }
}
