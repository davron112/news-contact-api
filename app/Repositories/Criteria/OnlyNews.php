<?php

namespace App\Repositories\Criteria;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PendingApprovalCreativesCriteria
 */
class OnlyNews extends RequestCriteria
{
    /**
     * Apply criteria in query repository
     *
     * @param \Prettus\Repository\Contracts\RepositoryInterface $model
     * @param \Prettus\Repository\Contracts\RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $repository = false;

        return $model->select('articles.*')->join('categories', 'categories.id', '=', 'articles.category_id')
            ->where('categories.slug', 'news');
    }
}
