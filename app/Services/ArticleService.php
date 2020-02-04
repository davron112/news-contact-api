<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Helpers\FileHelper;
use App\Models\Article;
use App\Models\Language;
use App\Repositories\Contracts\ArticleRepository;
use App\Services\Contracts\ArticleService as ArticleServiceInterface;
use App\Services\Traits\ServiceTranslateTable;
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
     * @var FileHelper $fileHelper
     */
    protected $fileHelper;

    /**
     * ArticleService constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param ArticleRepository $repository
     * @param Language $language
     * @param Logger $logger
     * @param FileHelper $fileHelper
     */
    public function __construct(
        DatabaseManager $databaseManager,
        ArticleRepository $repository,
        Language $language,
        Logger $logger,
        FileHelper $fileHelper
    ) {

        $this->databaseManager     = $databaseManager;
        $this->repository     = $repository;
        $this->logger     = $logger;
        $this->language     = $language;
        $this->fileHelper     = $fileHelper;
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
            $attributes = $this->storeImage($data);
            $article->slug = array_get($data, 'slug', Str::random(9));
            $article->status = array_get($data, 'status', 1);
            $article->category_id = array_get($data, 'category_id');
            $article->published_at = array_get($data, 'published_at');
            $article->author = array_get($data, 'author');
            $article->image = array_get($data, 'img');
            $article->fill($attributes);
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

    protected function storeImage(array $data){

        $dataFields =[];
        if(Arr::has($data,'img')) {
            $uploadedFile  = $data['img'];
            $dataFields['img'] = $this->fileHelper->upload($uploadedFile,'img\content');
        }
        return $dataFields;
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
            if (!$article->update($data)) {
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
