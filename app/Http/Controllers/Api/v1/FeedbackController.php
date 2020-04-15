<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Repositories\Contracts\FeedbackRepository;
use App\Services\Contracts\FeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

/**
 * Class FeedbackController
 * @package App\Http\Controllers\Api\v1
 */
class FeedbackController extends Controller
{
    /**
     * @var FeedbackRepository $repository
     */
    protected $repository;

    /**
     * @var string $modelName
     */
    private $modelName = 'Feedback';

    /**
     * @var string $modelNameMultiple
     */
    private $modelNameMultiple = 'Feedback';
    /**
     * @var FeedbackService $service
     */
    protected $service;

    /**
     * FeedbackController constructor.
     * @param FeedbackRepository $repository
     * @param FeedbackService $service
     */
    public function __construct(
        FeedbackRepository $repository,
        FeedbackService $service
    ){
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Show Feedback.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        return response(
            $this->successResponse(
                $this->modelNameMultiple,
                $this->repository->all()
            )
        );
    }


    /**
     * Create a new feedback
     *
     * @param Request $request
     * @param Logger $log
     * @return JsonResponse
     */
    public function store(
        Request $request,
        Logger $log
    )
    {
        $data = $request->all();
        $model = $this->service->store($data);

        if ($model){
            $message = $this->modelName .' was successfully stored.';
            $log->info($message, ['res' => $model]);
            $data = $this->successResponse($this->modelName, $model, $message);
        } else {
            $message = $this->modelName.' was not stored.';
            $log->error($message);
            $data = $this->errorResponse($this->modelName, null, $message);
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Show item
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $model = $this->repository->find($id);
        $data = $this->successResponse($this->modelName, $model);
        return response()->json($data, $data['code']);
    }

    /**
     * Show item
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     */
    public function countFeedbackItems()
    {
        $count = $this->repository->whereNotIn('status', [0])->get()->count();
        $data = $this->successResponse($this->modelName, ['count' => $count]);
        return response()->json($data, $data['code']);
    }

    /**
     * Show item
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     */
    public function checkOtp(Request $request)
    {
        $data = $request->all();

        $sid = array_get($data, 'sid');
        $otp = array_get($data, 'otp');
        $feedback = Feedback::where([
            ['sid', '=', $sid],
            ['otp', '=', $otp],
        ])->first();

        if ($feedback) {
            $feedback->status = 1;
            $feedback->save();
            $result = [
                'status' => 1,
                'type' => 'success',
                'message' => 'Success',
            ];
            $this->sendCongrulations([
                'recipient_number' => "+". (int) $feedback->phone,
                'message' => 'Sizning murojaatingiz qabul qilindi. Yaqin vaqt ichida murojaatingizga javob beriladi. www.beruniy-murojaat.uz',
                'app_id' => config('services.sms.app_id')
            ]);
        } else {
            $result = [
                'status' => 0,
                'type' => 'error',
                'message' => 'Incorrect password!',
            ];
        }

        return response()->json($result, !!$feedback ? 200 : 500);
    }

    /**
     * @param array $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendCongrulations(array $data)
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
}
