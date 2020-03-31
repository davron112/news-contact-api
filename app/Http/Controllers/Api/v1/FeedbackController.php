<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
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
            $log->info($message, ['id' => $model->id]);
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
        $count = $this->repository->get()->count();
        $data = $this->successResponse($this->modelName, ['count' => $count]);
        return response()->json($data, $data['code']);
    }
}
