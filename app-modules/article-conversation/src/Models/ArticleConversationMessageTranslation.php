<?php

namespace Modules\ArticleConversation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleConversationMessageTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_conversation_message_id',
        'bn_message',
        'hi_message',
        'es_message',
    ];

    /**
     * Get the message that owns the translation.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(ArticleConversationMessage::class, 'article_conversation_message_id');
    }
}
