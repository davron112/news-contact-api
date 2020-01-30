<?php

namespace App\Models;

use App\Models\Traits\TableName;
use App\Models\Traits\TranslationTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
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
    protected $translatableModel = CategoryTranslations::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'parent_id',
        'status',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * The languages that belong to the category item.
     *
     * @return BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, CategoryTranslations::getTableName(), 'item_id');
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
        return 'No translate';
    }
}
