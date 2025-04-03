<?php

namespace Modules\Word\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'word',
        'slug',
    ];

    /**
     * Get the meanings for the word.
     */
    public function meanings(): HasMany
    {
        return $this->hasMany(WordMeaning::class);
    }

    /**
     * Get the pronunciation for the word.
     */
    public function pronunciation()
    {
        return $this->hasOne(WordPronunciation::class);
    }

    /**
     * Get all connected words for this word (as word_id_1).
     */
    public function connections()
    {
        return $this->belongsToMany(Word::class, 'word_connections', 'word_id_1', 'word_id_2')
            ->withPivot('type');
    }

    /**
     * Get all connected words for this word (as word_id_2).
     */
    public function connectionsInverse()
    {
        return $this->belongsToMany(Word::class, 'word_connections', 'word_id_2', 'word_id_1')
            ->withPivot('type');
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
}
