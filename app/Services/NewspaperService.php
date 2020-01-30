<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Models\Newspaper;
use App\Models\Language;
use App\Repositories\Contracts\NewspaperRepository;
use App\Services\Contracts\NewspaperService as NewspaperServiceInterface;
use App\Services\Traits\ServiceTranslateTable;
use App\Exceptions\NotFoundException;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method bool destroy
 */
class NewspaperService  extends BaseService implements NewspaperServiceInterface
{
    use ServiceTranslateTable;

    /**
     * @var DatabaseManager $databaseManager
     */
    protected $databaseManager;

    /**
     * @var NewspaperRepository $repository
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
     * Newspaper constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param NewspaperRepository $repository
     * @param Language $language
     * @param Logger $logger
     */
    public function __construct(
        DatabaseManager $databaseManager,
        NewspaperRepository $repository,
        Language $language,
        Logger $logger
    ) {

        $this->databaseManager     = $databaseManager;
        $this->repository     = $repository;
        $this->logger     = $logger;
        $this->language     = $language;
    }

    /**
     * Create newspaper
     *
     * @param array $data
     * @return Newspaper
     * @throws \Exception
     */
    public function store(array $data)
    {

        $this->beginTransaction();

        try {
            $newspaper           = $this->repository->newInstance();
            $newspaper->slug = array_get($data, 'slug', Str::random(9));
            $newspaper->status     = array_get($data, 'status', 1);
            $newspaper->file     = array_get($data, 'file');
            $newspaper->img     = array_get($data, 'img');
            $newspaper->published_at     = array_get($data, 'published_at', Carbon::now());

            if (!$newspaper->save()) {
                throw new UnexpectedErrorException('Newspaper was not saved to the database.');
            }
            $this->logger->info('Newspaper was successfully saved to the database.');

            $this->storeTranslations($newspaper, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Translations for the Newspaper were successfully saved.', ['newspaper_id' => $newspaper->id]);
            return $newspaper;
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while storing an ', [
                'data' => $data,
            ]);
        }

        $this->commit();
    }

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     *
     * @return Newspaper
     *
     * @throws
     */
    public function update($id, array $data)
    {
        $this->beginTransaction();

        try {
            $newspaper = $this->repository->find($id);

            $newspaper->slug = array_get($data, 'slug', Str::random(9));
            $newspaper->status     = array_get($data, 'status', 1);
            $newspaper->file     = array_get($data, 'file');
            $newspaper->img     = array_get($data, 'img');
            $newspaper->published_at     = array_get($data, 'published_at', $newspaper->published_at);

            if (!$newspaper->save()) {
                throw new UnexpectedErrorException('An error occurred while updating a newspaper');
            }
            $this->logger->info('Newspaper was successfully updated.');

            $this->storeTranslations($newspaper, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Newspaper translations was successfully updated.');

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while updating an articles.', [
                'id'   => $id,
                'data' => $data,
            ]);

        }
        $this->commit();
        return $this->findOne($id);
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
            $bufferNewspaper = [];
            $newspaper = $this->repository->find($id);

            $bufferNewspaper['id'] = $newspaper->id;
            $bufferNewspaper['name'] = $newspaper->name;

            if (!$newspaper->delete($id)) {
                throw new UnexpectedErrorException(
                    'Newspaper and newspaper translations was not deleted from database.'
                );
            }
            $this->logger->info('Newspaper newspaper was successfully deleted from database.');
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while deleting an newspaper.', [
                'id'   => $id,
            ]);
        }
        $this->commit();
        return $bufferNewspaper;
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
                'title' => Arr::get($translation, 'name'),
            ];
        };
    }
}
