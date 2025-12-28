<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentPart extends Model
{
    protected $fillable = [
        'document_id',
        'part_name',
        'content',
        'order',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
