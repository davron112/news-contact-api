<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\FeedbackRepository;
use App\Services\Contracts\FeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

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
     * @return JsonResponse
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
        $model = $this->service->store($request->all());

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
     * Update a feedback
     *
     * @param Request $request
     * @param Logger $log
     *
     * @return JsonResponse
     */
    public function update(
        Request $request,
        Logger $log
    )
    {
        $id = $request->input('id');
        $model = $this->service->update($id, $request->all());

        if ($model){
            $message = $this->modelName .' was successfully updated.';
            $log->info($message, ['id' => $id]);
            $data = $this->successResponse($this->modelName, $model, $message);
        } else {
            $message = $this->modelName.' was not updated.';
            $log->error($message);
            $data = $this->errorResponse($this->modelName, null, $message);
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Show feedback
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(
        Request $request
    )
    {
        $modelId = $request->route('id');
        $model = $this->repository->find($modelId);
        $data = $this->successResponse($this->modelName, $model);
        return response()->json($data, $data['code']);
    }

    /**
     * Change status article
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function changeStatus($id, Request $request)
    {
        $data = $request->all();
        $status = array_get($data, 'status', 0);

        $feedback = $this->repository->find($id);

        $feedback->status = $status;
        $response = $this->successResponse($this->modelName, $feedback->save());
        return response()->json($response, $response['code']);
    }

    /**
     * Delete item
     *
     * @param Request $request
     * @param Logger $log
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        Logger $log
    )
    {
        $id = $request->input('id');
        $model = $this->service->delete($id);

        if ($model){
            $message = $this->modelName . ' was successfully deleted.';
            $log->info($message, ['id' => $id]);
            $data = $this->successResponse($this->modelName, $model, $message);
        } else {
            $message = $this->modelName . ' was not deleted.';
            $log->error($message);
            $data = $this->errorResponse($this->modelName, null, $message);
        }

        return response()->json($data, $data['code']);
    }

}
