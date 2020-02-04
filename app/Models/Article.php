<?php

namespace App\Models;

use App\Models\Traits\TableName;
use App\Models\Traits\TranslationTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Article
 * @package App\Models
 */
class Article extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    const STATUS_ARCHIVED = 3;

    use TableName, TranslationTable;

    protected $appends = ['title', 'description', 'content', 'img'];

    protected $dates = ['published_at'];

    /**
     * Related model that stores translations for the model.
     *
     * @var string
     */
    protected $translatableModel = ArticleTranslations::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'author',
        'published_at',
        'category_id',
        'status',
        'image',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * The languages that belong to the article item.
     *
     * @return BelongsToMany
     */
    public function languages()
    {
        return $this->belongsToMany(Language::class, ArticleTranslations::getTableName(), 'item_id');
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

    /**
     * Get translated title.
     *
     * @return string
     */
    public function getImgAttribute()
    {
        return config('filesystems.disks.public.url') . '/' .$this->image;
    }

    /**
     * Get translated description.
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        if ($trans = $this->translate('description')) {
            return $trans;
        }
        return 'No translate';
    }

    /**
     * Get translated content.
     *
     * @return string
     */
    public function getContentAttribute()
    {
        if ($trans = $this->translate('content')) {
            return $trans;
        }
        return 'No translate';
    }

    /**
     * Get translated source.
     *
     * @return string
     */
    public function getSourceAttribute()
    {
        if ($trans = $this->translate('source')) {
            return $trans;
        }
        return 'No translate';
    }

    /**
     * Get translated source.
     *
     * @return string
     */
    public function getUrlImgAttribute()
    {
        return config('filesystems.disks.public.url') . $this->img;
    }

    /**
     * Get the category.
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
