<?php

namespace App\Models;

use App\Models\Traits\TableName;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Feedback
 * @package App\Models
 * NewspaperTranslations $translationsAll
 */
class Feedback extends Model
{
    use TableName;

    /**
     * Status disabled
     */
    const STATUS_DRAFT = 0;

    /**
     * Active status
     */
    const STATUS_PROCESS = 1;

    /**
     * Active status
     */
    const STATUS_VIEWED = 2;

    /**
     * Active status
     */
    const STATUS_REJECT = 3;

    /**
     * Active status
     */
    const STATUS_COMPLETED = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'fio',
        'phone',
        'email',
        'address',
        'department',
        'subject',
        'message',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
