<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Criteria\OnlyNews;
use Illuminate\Log\Logger;
use Illuminate\Container\Container as App;
use App\Repositories\Contracts\ArticleRepository as ArticleRepositoryInterface;

/**
 * Class ArticleRepository
 * @package App\Repositories
 */
class ArticleRepository extends Repository implements ArticleRepositoryInterface
{
    /**
     * Article constructor.
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
        return Article::class;
    }

    /**
     * Filter by slug news
     *
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function filterNews()
    {
        $this
            ->selectRaw('articles.*')
            ->join('categories', 'categories.id', '=', 'articles.category_id')
            ->where('categories.slug', 'news');

        return $this;
    }

}
