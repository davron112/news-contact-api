<?php

namespace App\Models;

use App\Models\Traits\TableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{

    use SoftDeletes;

    const ID_DEFAULT    = 1;
    const ID_UZBEK_LATIN  = 1;
    const ID_RUSSIAN    = 2;
    const ID_ENGLISH = 3;
    const ID_UZBEK_CYRILLIC = 4;

    use TableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['long_name', 'short_name'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the category for the language.
     *
     * @return HasMany
     */
    public function categoryTranslations(): HasMany
    {
        return $this->hasMany(CategoryTranslations::class);
    }

    /**
     * Get the article for the language.
     *
     * @return HasMany
     */
    public function articleTranslations(): HasMany
    {
        return $this->hasMany(ArticleTranslations::class);
    }

    /**
     * Get the newspaper for the language.
     *
     * @return HasMany
     */
    public function newspaperTranslations(): HasMany
    {
        return $this->hasMany(NewspaperTranslations::class);
    }

    /**
     * Get the tag for the language.
     *
     * @return HasMany
     */
    public function tagTranslations(): HasMany
    {
        return $this->hasMany(TagTranslations::class);
    }

}
