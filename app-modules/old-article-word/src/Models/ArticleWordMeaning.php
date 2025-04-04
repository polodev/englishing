<?php

namespace Modules\ArticleWord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleWordMeaning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_word_set_list_id',
        'meaning',
        'display_order',
    ];

    /**
     * Get the list that owns the meaning.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(ArticleWordSetList::class, 'article_word_set_list_id');
    }

    /**
     * Get the translation for the meaning.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleWordMeaningTranslation::class);
    }
}
