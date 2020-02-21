<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Log\Logger;
use Illuminate\Container\Container as App;
use App\Repositories\Contracts\VideoRepository as VideoRepositoryInterface;

/**
 * Class VideoRepository
 * @package App\Repositories
 */
class VideoRepository extends Repository implements VideoRepositoryInterface
{
    /**
     * Video constructor.
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
        return Video::class;
    }

}
