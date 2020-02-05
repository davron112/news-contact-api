<?php

namespace App\Models;

use App\Models\Traits\TableName;
use App\Models\Traits\TranslationTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Article
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property Category $category
 * @property ArticleTranslations $translations
 * @package App\Models
 */
class Article extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    const STATUS_ARCHIVED = 3;

    use TableName, TranslationTable;

    protected $appends = ['title', 'description', 'content', 'url'];

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
        'img',
        'category_id',
        'status',
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
        return 'No translate';
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
     * Get the category.
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return "/categories/" . $this->category->slug .'/'. $this->slug;
    }

}
