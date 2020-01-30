<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Models\Article;
use App\Models\Language;
use App\Repositories\Contracts\ArticleRepository;
use App\Services\Contracts\ArticleService as ArticleServiceInterface;
use App\Services\Traits\ServiceTranslateTable;
use App\Exceptions\NotFoundException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method bool destroy
 */
class ArticleService  extends BaseService implements ArticleServiceInterface
{
    use ServiceTranslateTable;

    /**
     * @var DatabaseManager $databaseManager
     */
    protected $databaseManager;

    /**
     * @var ArticleRepository $repository
     */
    protected $repository;

    /**
     * Language $language
     */
    protected $language;

    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * Article constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param ArticleRepository $repository
     * @param Language $language
     * @param Logger $logger
     */
    public function __construct(
        DatabaseManager $databaseManager,
        ArticleRepository $repository,
        Language $language,
        Logger $logger
    ) {

        $this->databaseManager     = $databaseManager;
        $this->repository     = $repository;
        $this->logger     = $logger;
        $this->language     = $language;
    }

    /**
     * Create article
     *
     * @param array $data
     * @return Article
     * @throws \Exception
     */
    public function store(array $data)
    {
        $this->beginTransaction();
        try {
            $article = $this->repository->newInstance();
            $article->slug = array_get($data, 'slug', Str::random(9));
            $article->status = array_get($data, 'status', 1);
            $article->category_id = array_get($data, 'category_id');
            $article->published_at = array_get($data, 'published_at');
            $article->author = array_get($data, 'author');
            $article->img = array_get($data, 'img');

            if (!$article->save()) {
                throw new UnexpectedErrorException('Article was not saved to the database.');
            }
            $this->logger->info('Article was successfully saved to the database.');

            $this->storeTranslations($article, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Translations for the Article were successfully saved.', ['article_id' => $article->id]);
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while storing an ', [
                'data' => $data,
            ]);
        }

        $this->commit();
        return $article;
    }

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     *
     * @return Article
     *
     * @throws
     */
    public function update($id, array $data)
    {
        $this->beginTransaction();
        try {
            $article = $this->repository->find($id);
            $article->slug = array_get($data, 'slug', $article->slug);
            $article->status = array_get($data, 'status', 1);
            $article->category_id = array_get($data, 'category_id');
            $article->published_at = array_get($data, 'published_at');
            $article->author = array_get($data, 'author');
            $article->img = array_get($data, 'img');

            if (!$article->save()) {
                throw new UnexpectedErrorException('An error occurred while updating a article');
            }
            $this->logger->info('Article was successfully updated.');

            $this->storeTranslations($article, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Article translations was successfully updated.');

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while updating an articles.', [
                'id'   => $id,
                'data' => $data,
            ]);

        }
        $this->commit();
        return $article;
    }
    /**
     * Delete block in the storage.
     *
     * @param  int  $id
     *
     * @return array
     *
     * @throws
     */
    public function delete($id)
    {

        $this->beginTransaction();

        try {
            $bufferArticle = [];
            $article = $this->repository->find($id);

            $bufferArticle['id'] = $article->id;
            $bufferArticle['name'] = $article->name;

            if (!$article->delete($id)) {
                throw new UnexpectedErrorException(
                    'Article and article translations was not deleted from database.'
                );
            }
            $this->logger->info('Article article was successfully deleted from database.');
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while deleting an article.', [
                'id'   => $id,
            ]);
        }
        $this->commit();
        return $bufferArticle;
    }

    /**
     * Closure that handles translation for storing in the database.
     *
     * @return \Closure
     */
    protected function getTranslationSelectColumnsClosure()
    {
        return function ($translation) {
            return [
                'title' => Arr::get($translation, 'title'),
                'description' => Arr::get($translation, 'description'),
                'content' => Arr::get($translation, 'content'),
                'source' => Arr::get($translation, 'source'),
            ];
        };
    }
}
