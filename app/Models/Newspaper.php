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
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    const STATUS_ARCHIVED = 3;

    use TableName, TranslationTable;

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
        'published_at',
    ];
    /**
     * @var array
     */
    protected $visible = [
        'id',
        'file',
        'img',
        'status',
        'published_at',
        'title',
        'translations',
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
     * Get translated title.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        if ($trans = $this->translate('title')) {
            return $trans;
        }
        return 'No translate';
    }
}
