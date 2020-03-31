<?php

namespace App\Repositories\Contracts;

/**
 * Interface ArticleRepository
 * @package App\Repositories\Contracts
 */
interface ArticleRepository extends Repository
{

    /**
     * Filter by slug news
     *
     * @return \App\Repositories\ArticleRepository
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function filterNews();
}
