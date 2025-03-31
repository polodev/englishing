<?php

namespace Modules\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'section_id',
        'bn_title',
        'hi_title',
        'es_title',
        'bn_content',
        'hi_content',
        'es_content',
    ];

    /**
     * Get the section that owns the translation.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
