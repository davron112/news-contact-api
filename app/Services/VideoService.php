<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Helpers\FileHelper;
use App\Models\Video;
use App\Models\Language;
use App\Repositories\Contracts\VideoRepository;
use App\Services\Contracts\VideoService as VideoServiceInterface;
use App\Services\Traits\ServiceTranslateTable;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;

/**
 * @method bool destroy
 */
class VideoService  extends BaseService implements VideoServiceInterface
{
    use ServiceTranslateTable;

    /**
     * @var DatabaseManager $databaseManager
     */
    protected $databaseManager;

    /**
     * @var VideoRepository $repository
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
     * VideoService constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param VideoRepository $repository
     * @param Language $language
     * @param Logger $logger
     * @param FileHelper $fileHelper
     */
    public function __construct(
        DatabaseManager $databaseManager,
        VideoRepository $repository,
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
     * Create video
     *
     * @param array $data
     * @return Video
     * @throws \Exception
     */
    public function store(array $data)
    {
        $this->beginTransaction();

        try {
            $video = $this->repository->newInstance();
            $attributes = $this->storeFiles($data);
            $video->status = array_get($data, 'status', 1);
            $video->fill($attributes);
            if ($video->img) {
                $video->img = config('filesystems.disks.public.url') . preg_replace('#public#', '', $video->img);
            }
            if ($video->file) {
                $video->file = config('filesystems.disks.public.url') . preg_replace('#public#', '', $video->file);
            }
            $video->published_at     = array_get($data, 'published_at', Carbon::now());

            if (!$video->save()) {
                throw new UnexpectedErrorException('Video was not saved to the database.');
            }
            $this->logger->info('Video was successfully saved to the database.');

            $this->storeTranslations($video, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Translations for the Video were successfully saved.', ['video_id' => $video->id]);

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while storing an ', [
                'data' => $data,
            ]);
        }
        $this->commit();
        return $video;
    }

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     *
     * @return Video
     *
     * @throws
     */
    public function update($id, array $data)
    {
        $this->beginTransaction();

        try {
            $video = $this->repository->find($id);
            if (array_get($data, 'img')) {
                $attributes = $this->storeFiles($data);
                $video->fill($attributes);
                if ($video->img) {
                    $data['img'] = config('filesystems.disks.public.url') . preg_replace('#public#', '', $video->img);
                }
            }
            if (!$video->update($data)) {
                throw new UnexpectedErrorException('An error occurred while updating a video');
            }
            $this->logger->info('Video was successfully updated.');

            $this->storeTranslations($video, $data, $this->getTranslationSelectColumnsClosure());
            $this->logger->info('Video translations was successfully updated.');

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while updating an articles.', [
                'id'   => $id,
                'data' => $data,
            ]);

        }
        $this->commit();
        return $video;
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
            $bufferVideo = [];
            $video = $this->repository->find($id);

            $bufferVideo['id'] = $video->id;
            $bufferVideo['name'] = $video->name;

            if (!$video->delete($id)) {
                throw new UnexpectedErrorException(
                    'Video and video translations was not deleted from database.'
                );
            }
            $this->logger->info('Video video was successfully deleted from database.');
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while deleting an video.', [
                'id'   => $id,
            ]);
        }
        $this->commit();
        return $bufferVideo;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function storeFiles(array $data){

        $dataFields =[];
        if(Arr::has($data,'img')) {
            $uploadedFile  = $data['img'];
            $dataFields['img'] = $this->fileHelper->upload($uploadedFile,'public\img\content');
        }

        if(Arr::has($data,'file')) {
            $uploadedFile  = $data['file'];
            $dataFields['file'] = $this->fileHelper->upload($uploadedFile,'public\pdf\content');
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
