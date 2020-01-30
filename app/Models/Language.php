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
     * Get the age category for the language.
     *
     * @return HasMany
     */
    public function ageCategoryTranslation(): HasMany
    {
        return $this->hasMany(AgeCategoryTranslations::class);
    }

    /**
     * Get the age appealer category for the language.
     *
     * @return HasMany
     */
    public function appealerCategoryTranslation(): HasMany
    {
        return $this->hasMany(AppealerCategoryTranslations::class);
    }

    /**
     * Get the appeal type category for the language.
     *
     * @return HasMany
     */
    public function appealTypeTranslation(): HasMany
    {
        return $this->hasMany(AppealTypeTranslations::class);
    }

    /**
     * Get the authority for the language.
     *
     * @return HasMany
     */
    public function authorityTranslation(): HasMany
    {
        return $this->hasMany(AuthorityTranslations::class);
    }

    /**
     * Get the infosystem for the language.
     *
     * @return HasMany
     */
    public function infosystemTranslation(): HasMany
    {
        return $this->hasMany(InfosystemTranslations::class);
    }

    /**
     * Get the period type for the language.
     *
     * @return HasMany
     */
    public function periodTypeTranslation(): HasMany
    {
        return $this->hasMany(PeriodTypeTranslations::class);
    }

    /**
     * Get the region for the language.
     *
     * @return HasMany
     */
    public function regionTranslation(): HasMany
    {
        return $this->hasMany(RegionTranslations::class);
    }

    /**
     * Get the regulatory document for the language.
     *
     * @return HasMany
     */
    public function regulatoryDocumentTranslation(): HasMany
    {
        return $this->hasMany(RegulatoryDocumentTranslations::class);
    }
    /**
     * Get the service contact for the language.
     *
     * @return HasMany
     */
    public function serviceContactTranslation(): HasMany
    {
        return $this->hasMany(ServiceContactTranslations::class);
    }
    /**
     * Get the course for the language.
     *
     * @return HasMany
     */
    public function courseTranslation(): HasMany
    {
        return $this->hasMany(CourseTranslations::class);
    }
    /**
     * Get the service type for the language.
     *
     * @return HasMany
     */
    public function serviceTypeTranslation(): HasMany
    {
        return $this->hasMany(ServiceTypeTranslations::class);
    }
    /**
     * Get the sphere for the language.
     *
     * @return HasMany
     */
    public function sphereTranslation(): HasMany
    {
        return $this->hasMany(SphereTranslations::class);
    }
}
