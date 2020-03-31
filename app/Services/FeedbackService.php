<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Helpers\FileHelper;
use App\Models\feedback;
use App\Models\Language;
use App\Repositories\Contracts\feedbackRepository;
use App\Services\Contracts\FeedbackService as FeedbackServiceInterface;
use App\Services\Traits\ServiceTranslateTable;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;

/**
 * @method bool destroy
 */
class FeedbackService  extends BaseService implements FeedbackServiceInterface
{
    use ServiceTranslateTable;

    /**
     * @var DatabaseManager $databaseManager
     */
    protected $databaseManager;

    /**
     * @var FeedbackRepository $repository
     */
    protected $repository;

    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * @var Logger $logger
     */
    protected $fileHelper;

    /**
     * feedbackService constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param feedbackRepository $repository
     * @param Logger $logger
     * @param FileHelper $fileHelper
     */
    public function __construct(
        DatabaseManager $databaseManager,
        feedbackRepository $repository,
        Logger $logger,
        FileHelper $fileHelper
    ) {

        $this->databaseManager     = $databaseManager;
        $this->repository     = $repository;
        $this->logger     = $logger;
        $this->fileHelper     = $fileHelper;
    }

    /**
     * Create feedback
     *
     * @param array $data
     * @return Feedback
     * @throws \Exception
     */
    public function store(array $data)
    {
        $this->beginTransaction();

        try {
            $feedback = $this->repository->newInstance();
            //$attributes = $this->storeFiles($data);
            $feedback->fill($data);
            $feedback->status = Feedback::STATUS_DRAFT;
            /*
            if ($feedback->file) {
                $feedback->file = config('filesystems.disks.public.url') . preg_replace('#public#', '', $feedback->file);
            }*/

            if (!$feedback->save()) {
                throw new UnexpectedErrorException('feedback was not saved to the database.');
            }

            $this->logger->info('feedback was successfully saved to the database.');

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while storing an ', [
                'data' => $data,
            ]);
        }
        $this->commit();
        return $feedback;
    }

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     *
     * @return feedback
     *
     * @throws
     */
    public function update($id, array $data)
    {
        $this->beginTransaction();

        try {
            /** @var Feedback $feedback */
            $feedback = $this->repository->find($id);

            if (!$feedback) {
                throw new \Exception('Not found. ' . $id);
            }

            /*if (array_get($data, 'file')) {
                $attributes = $this->storeFiles($data);
                $feedback->file = config('filesystems.disks.public.url') . preg_replace('#public#', '', $feedback->file);
            }*/

            $feedback->fill($data);

            if (!$feedback->save()) {
                throw new UnexpectedErrorException('An error occurred while updating a feedback');
            }

            $tagIds = array_get($data, 'tags');
            if ($tagIds) {
                $feedback->tags()->sync(explode(',', $tagIds));
            }

            $this->logger->info('Feedback was successfully updated.');

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while updating an articles.', [
                'id'   => $id,
                'data' => $data,
            ]);

        }
        $this->commit();
        return $feedback;
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
            $bufferFeedback = [];
            $feedback = $this->repository->find($id);

            $bufferFeedback['id'] = $feedback->id;

            if (!$feedback->delete($id)) {
                throw new UnexpectedErrorException(
                    'feedback and feedback translations was not deleted from database.'
                );
            }
            $this->logger->info('feedback feedback was successfully deleted from database.');
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while deleting an feedback.', [
                'id'   => $id,
            ]);
        }
        $this->commit();
        return $bufferFeedback;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function storeFiles(array $data){

        $dataFields =[];
        $uploadedImage = Arr::get($data,'img');
        if($uploadedImage) {
            $dataFields['img'] = $this->fileHelper->upload($uploadedImage,'public\img\content');
        }

        $uploadedFile = Arr::get($data,'file');
        if($uploadedFile) {
            $dataFields['file'] = $this->fileHelper->upload($uploadedFile,'public\pdf\content');
        }
        return $dataFields;
    }
}
