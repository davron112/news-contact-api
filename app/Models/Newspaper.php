<?php

namespace App\Models;

use App\Models\Traits\TableName;
use App\Models\Traits\TranslationTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Newspaper
 * @package App\Models
 * NewspaperTranslations $translationsAll
 */
class Newspaper extends Model
{
    use TableName, TranslationTable;

    /**
     * Active status
     */
    const STATUS_ACTIVE = 1;

    /**
     * Status disabled
     */
    const STATUS_DISABLED = 2;

    /**
     * Status archived
     */
    const STATUS_ARCHIVED = 3;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['title', 'translations'];

    /**
     * Related model that stores translations for the model.
     *
     * @var string
     */
    protected $translatableModel = NewspaperTranslations::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file',
        'img',
        'status',
        'number',
        'published_at',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'file',
        'img',
        'status',
        'number',
        'published_at',
        'title',
        'created_at',
        'updated_at',
        'translations',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * The languages that belong to the newspaper item.
     *
     * @return BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, NewspaperTranslations::getTableName(), 'item_id');
    }

    /**
     * Get all of the tags for the post.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get translated title.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        if ($trans = $this->translate('title')) {
            return $trans;
        }
        return '';
    }
}
