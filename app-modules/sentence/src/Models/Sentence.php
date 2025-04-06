<?php

namespace Modules\Sentence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Sentence extends Model
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
        'pronunciation',
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
     * Get the translations for the sentence.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SentenceTranslation::class);
    }

    /**
     * Get translation for a specific locale.
     *
     * @param string $locale
     * @return \Modules\Sentence\Models\SentenceTranslation|null
     */
    public function getTranslation(string $locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }
}
