<?php

namespace App\Repositories;

use App\Models\Newspaper;
use Illuminate\Log\Logger;
use Illuminate\Container\Container as App;
use App\Repositories\Contracts\NewspaperRepository as NewspaperRepositoryInterface;

class NewspaperRepository extends Repository implements NewspaperRepositoryInterface
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
        return Newspaper::class;
    }

}
