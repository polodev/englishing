<?php

namespace Modules\ArticleDoubleSentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleDoubleSentenceTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_double_sentence_set_list_id',
        'sentence_1_bn_meaning',
        'sentence_1_hi_meaning',
        'sentence_1_es_meaning',
        'sentence_2_bn_meaning',
        'sentence_2_hi_meaning',
        'sentence_2_es_meaning',
    ];

    /**
     * Get the double sentence list that owns the translation.
     */
    public function doubleSentenceList(): BelongsTo
    {
        return $this->belongsTo(ArticleDoubleSentenceSetList::class, 'article_double_sentence_set_list_id');
    }
}
