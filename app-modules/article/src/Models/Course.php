<?php

namespace Modules\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;

class Course extends Model
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
        'title_translation',
        'content_translation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title_translation' => 'array',
        'content_translation' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            // Generate slug from title if not already set
            if (empty($course->slug) && !empty($course->title)) {
                $course->slug = Str::slug($course->title);
            }
        });

        static::updating(function ($course) {
            // Update slug when title changes
            if ($course->isDirty('title')) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    /**
     * Get the user that owns the course.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Get the articles for the course.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
