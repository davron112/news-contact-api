<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Helpers\FileHelper;
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
     * @var Logger $logger
     */
    protected $fileHelper;

    /**
     * NewspaperService constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param NewspaperRepository $repository
     * @param Language $language
     * @param Logger $logger
     * @param FileHelper $fileHelper
     */
    public function __construct(
        DatabaseManager $databaseManager,
        NewspaperRepository $repository,
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
            $attributes = $this->storeImage($data);
            $newspaper->status = array_get($data, 'status', 1);
            $newspaper->file = array_get($data, 'file');
            $newspaper->img = array_get($data, 'img');
            $newspaper->fill($attributes);
            $newspaper->published_at     = array_get($data, 'published_at', Carbon::now());

            if (!$newspaper->save()) {
                throw new UnexpectedErrorException('Newspaper was not saved to the database.');
            }
            $this->logger->info('Newspaper was successfully saved to the database.');

            $this->storeTranslations($newspaper, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Translations for the Newspaper were successfully saved.', ['newspaper_id' => $newspaper->id]);

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while storing an ', [
                'data' => $data,
            ]);
        }
        $this->commit();
        return $newspaper;
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
        return $newspaper;
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
     * @param array $data
     * @return array
     */
    protected function storeFiles(array $data){

        $dataFields =[];
        if(Arr::has($data,'img')) {
            $uploadedFile  = $data['img'];
            $dataFields['img'] = $this->fileHelper->upload($uploadedFile,'img\content');
        }

        if(Arr::has($data,'file')) {
            $uploadedFile  = $data['file'];
            $dataFields['file'] = $this->fileHelper->upload($uploadedFile,'file\content');
        }
        return $dataFields;
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
