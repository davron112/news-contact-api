<?php

namespace App\Models;

use App\Models\Traits\TableName;
use App\Models\Traits\TranslationTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    const STATUS_ARCHIVED = 3;

    use TableName, TranslationTable;

    protected $appends = ['name'];

    /**
     * Related model that stores translations for the model.
     *
     * @var string
     */
    protected $translatableModel = TagTranslations::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'status',
    ];

    protected $visible = [
        'slug',
        'status',
        'name',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * The languages that belong to the tag item.
     *
     * @return BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, TagTranslations::getTableName(), 'item_id');
    }

    /**
     * Get all of the articles that are assigned this tag.
     */
    public function articles()
    {
        return $this->morphedByMany(Article::class, 'taggable');
    }

    /**
     * Get all of the newspaper that are assigned this tag.
     */
    public function newspapers()
    {
        return $this->morphedByMany(Newspaper::class, 'taggable');
    }

    /**
     * Get all of the videos that are assigned this tag.
     */
    public function videos()
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
    /**
     * Get translated title.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        if ($trans = $this->translate('name')) {
            return $trans;
        }
        return '';
    }
}
