<?php

namespace App\Repositories;

use App\Models\Feedback;
use Illuminate\Log\Logger;
use Illuminate\Container\Container as App;
use App\Repositories\Contracts\FeedbackRepository as FeedbackRepositoryInterface;

/**
 * Class FeedbackRepository
 * @package App\Repositories
 */
class FeedbackRepository extends Repository implements FeedbackRepositoryInterface
{
    /**
     * Newspaper constructor.
     *
     * @param App $app
     * @param Logger $log
     */
    public function __construct(
        App $app,
        Logger $log
    ) {
        parent::__construct($app, $log);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Feedback::class;
    }

}
