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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
     * @var Client
     */
    protected $client;

    /**
     * Response content
     * @var mixed
     */
    protected $responseContent;

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
        FileHelper $fileHelper,
        Client $client
    ) {

        $this->databaseManager     = $databaseManager;
        $this->repository     = $repository;
        $this->logger     = $logger;
        $this->fileHelper     = $fileHelper;
        $this->client     = $client;
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

            $inputFile = array_get($data, 'file', false);
            if ($inputFile) {
                $attributes = $this->storeFiles($data);
            } else {
                $attributes = $data;
            }
            $feedback->fill($attributes);

            $otp = generate_otp(5);

            $feedback->otp = $otp;

            $res = $this->sendOtpSms([
                'recipient_number' => "+". (int) $feedback->phone,
                'message' => 'Tasdiqlash kodi: ' . $otp . '. www.beruniy-murojaat.uz',
                'app_id' => config('services.sms.app_id')
            ]);

            if ($inputFile && $feedback->file) {
                $feedback->file = config('filesystems.disks.public.url') . preg_replace('#public#', '', $feedback->file);
            }

            $feedback->status = Feedback::STATUS_DRAFT;

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
        return $res;
    }

    /**
     * @param array $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendOtpSms(array $data)
    {

        $url = 'https://smsapi.uz/api/v1/sms/send';
        $headers = [
            'Authorization: Bearer ' . config('services.sms.api_key'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
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

            if (array_get($data, 'file')) {
                $attributes = $this->storeFiles($data);
                $feedback->file = config('filesystems.disks.public.url') . preg_replace('#public#', '', $feedback->file);
            }

            $feedback->fill($attributes);
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

        $dataFields = $data;

        $uploadedFile = Arr::get($data,'file');

        if ($uploadedFile) {
            $dataFields['file'] = $this->fileHelper->upload($uploadedFile,'public\pdf\content');
        }
        return $dataFields;
    }
}
