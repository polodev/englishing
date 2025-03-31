<?php

namespace Modules\ArticleConversation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArticleConversationMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_conversation_id',
        'speaker',
        'message',
        'slug',
        'display_order',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ArticleConversation::class, 'article_conversation_id');
    }

    /**
     * Get the translation for the message.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(ArticleConversationMessageTranslation::class);
    }
}
