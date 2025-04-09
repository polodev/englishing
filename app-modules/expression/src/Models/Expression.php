<?php

namespace Modules\Expression\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Expression extends Model
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
     * Get the meanings for the expression.
     */
    public function meanings(): HasMany
    {
        return $this->hasMany(ExpressionMeaning::class);
    }

    /**
     * Get the translations for the expression.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ExpressionTranslation::class);
    }

    /**
     * Get all connected expressions for this expression (as expression_id_1).
     */
    public function connections(): BelongsToMany
    {
        return $this->belongsToMany(Expression::class, 'expression_connections', 'expression_id_1', 'expression_id_2')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get all connected expressions for this expression (as expression_id_2).
     */
    public function connectionsInverse(): BelongsToMany
    {
        return $this->belongsToMany(Expression::class, 'expression_connections', 'expression_id_2', 'expression_id_1')
            ->withPivot('type')
            ->withTimestamps();
    }
    
    /**
     * Get the synonyms for this expression.
     */
    public function synonyms()
    {
        // Get synonyms where this expression is expression_id_1
        $synonyms1 = $this->connections()->wherePivot('type', 'synonyms');
        
        // Get synonyms where this expression is expression_id_2
        $synonyms2 = $this->connectionsInverse()->wherePivot('type', 'synonyms');
        
        // Return all synonyms
        return $synonyms1->get()->merge($synonyms2->get());
    }

    /**
     * Get the antonyms for this expression.
     */
    public function antonyms()
    {
        // Get antonyms where this expression is expression_id_1
        $antonyms1 = $this->connections()->wherePivot('type', 'antonyms');
        
        // Get antonyms where this expression is expression_id_2
        $antonyms2 = $this->connectionsInverse()->wherePivot('type', 'antonyms');
        
        // Return all antonyms
        return $antonyms1->get()->merge($antonyms2->get());
    }
    
    /**
     * Get all synonyms for this expression.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSynonyms()
    {
        return $this->synonyms();
    }
    
    /**
     * Get all antonyms for this expression.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAntonyms()
    {
        return $this->antonyms();
    }
    
    /**
     * Attach a synonym to this expression.
     *
     * @param int|Expression $expression The expression to attach as a synonym
     * @return void
     */
    public function attachSynonym($expression)
    {
        $expressionId = $expression instanceof Expression ? $expression->id : $expression;
        $this->connections()->attach($expressionId, ['type' => 'synonyms']);
    }
    
    /**
     * Attach an antonym to this expression.
     *
     * @param int|Expression $expression The expression to attach as an antonym
     * @return void
     */
    public function attachAntonym($expression)
    {
        $expressionId = $expression instanceof Expression ? $expression->id : $expression;
        $this->connections()->attach($expressionId, ['type' => 'antonyms']);
    }
}
