<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Video;
use App\Repositories\Contracts\VideoRepository;
use App\Services\Contracts\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class VideoController extends Controller
{
    /**
     * @var VideoRepository $repository
     */
    protected $repository;

    /**
     * @var string $modelName
     */
    private $modelName = 'Video';

    /**
     * @var string $modelNameMultiple
     */
    private $modelNameMultiple = 'Videos';
    /**
     * @var VideoService $service
     */
    protected $service;

    /**
     * VideoController constructor.
     * @param VideoRepository $repository
     * @param VideoService $service
     */
    public function __construct(
        VideoRepository $repository,
        VideoService $service
    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        $videos = $this->repository->all();
        $videos->map(function (Video $video) {
            $video->addVisible('translations');
        });
        return response(
            $this->successResponse(
                $this->modelNameMultiple,
                $videos
            )
        );
    }

    /**
     * Create a new newspaper
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

        if ($model) {
            $message = $this->modelName . ' was successfully stored.';
            $log->info($message, ['id' => $model->id]);
            $data = $this->successResponse($this->modelName, $model, $message);
        } else {
            $message = $this->modelName . ' was not stored.';
            $log->error($message);
            $data = $this->errorResponse($this->modelName, null, $message);
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Update a newspaper
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

        if ($model) {
            $message = $this->modelName . ' was successfully updated.';
            $log->info($message, ['id' => $id]);
            $data = $this->successResponse($this->modelName, $model, $message);
        } else {
            $message = $this->modelName . ' was not updated.';
            $log->error($message);
            $data = $this->errorResponse($this->modelName, null, $message);
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Get one video
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     */
    public function show($locale, $id)
    {
        if (Language::where('short_name', '=', $locale)->first()) {
            app()->setLocale($locale);
        }
        $video = $this->repository->find($id);
        $video->addVisible('translations');
        $data = $this->successResponse($this->modelName, $video);
        return response()->json($data, $data['code']);
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

        if ($model) {
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
