<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Word extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
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
     * Get the meanings for the word.
     */
    public function meanings(): HasMany
    {
        return $this->hasMany(WordMeaning::class);
    }

    /**
     * Get the translations for the word.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(WordTranslation::class);
    }

    /**
     * Get all connected words for this word (as word_id_1).
     */
    public function connections(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'word_connections', 'word_id_1', 'word_id_2')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get all connected words for this word (as word_id_2).
     */
    public function connectionsInverse(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'word_connections', 'word_id_2', 'word_id_1')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get the synonyms for this word.
     */
    public function synonyms()
    {
        // Get synonyms where this word is word_id_1
        $synonyms1 = $this->connections()->wherePivot('type', 'synonyms');

        // Get synonyms where this word is word_id_2
        $synonyms2 = $this->connectionsInverse()->wherePivot('type', 'synonyms');

        // Return all synonyms
        return $synonyms1->get()->merge($synonyms2->get());
    }

    /**
     * Get the antonyms for this word.
     */
    public function antonyms()
    {
        // Get antonyms where this word is word_id_1
        $antonyms1 = $this->connections()->wherePivot('type', 'antonyms');

        // Get antonyms where this word is word_id_2
        $antonyms2 = $this->connectionsInverse()->wherePivot('type', 'antonyms');

        // Return all antonyms
        return $antonyms1->get()->merge($antonyms2->get());
    }

    /**
     * Get all synonyms for this word.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSynonyms()
    {
        return $this->synonyms();
    }

    /**
     * Get all antonyms for this word.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAntonyms()
    {
        return $this->antonyms();
    }

    /**
     * Attach a synonym to this word.
     *
     * @param int|Word $word The word to attach as a synonym
     * @return void
     */
    public function attachSynonym($word)
    {
        $wordId = $word instanceof Word ? $word->id : $word;
        $this->connections()->attach($wordId, ['type' => 'synonyms']);
    }

    /**
     * Attach an antonym to this word.
     *
     * @param int|Word $word The word to attach as an antonym
     * @return void
     */
    public function attachAntonym($word)
    {
        $wordId = $word instanceof Word ? $word->id : $word;
        $this->connections()->attach($wordId, ['type' => 'antonyms']);
    }

    /**
     * Get the list of available parts of speech.
     *
     * @return array
     */
    public static function getPartsOfSpeech(): array
    {
        return [
            'noun' => 'Noun',
            'verb' => 'Verb',
            'adjective' => 'Adjective',
            'adverb' => 'Adverb',
            'pronoun' => 'Pronoun',
            'preposition' => 'Preposition',
            'conjunction' => 'Conjunction',
            'interjection' => 'Interjection',
            'article' => 'Article',
            'determiner' => 'Determiner'
        ];
    }
}
