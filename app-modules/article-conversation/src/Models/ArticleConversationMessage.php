<?php

namespace Modules\ArticleConversation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ArticleConversationMessage extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        'pronunciation', # pronunciation should be for non english locale.
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pronunciation' => 'array',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ArticleConversation::class, 'article_conversation_id');
    }

    /**
     * Get the translations for the message.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ArticleConversationMessageTranslation::class);
    }

    /**
     * Get the translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\ArticleConversation\Models\ArticleConversationMessageTranslation|null
     */
    public function getMyTranslation(string $locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
