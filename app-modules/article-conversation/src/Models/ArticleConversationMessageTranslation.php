<?php

namespace Modules\ArticleConversation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleConversationMessageTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the message that owns the translation.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(ArticleConversationMessage::class, 'article_conversation_message_id');
    }
}
