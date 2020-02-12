<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Repositories\Contracts\TagRepository;
use App\Services\Contracts\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class TagsController extends Controller
{
    /**
     * @var TagRepository $repository
     */
    protected $repository;

    /**
     * @var string $modelName
     */
    private $modelName = 'Tag';

    /**
     * @var string $modelNameMultiple
     */
    private $modelNameMultiple = 'Tags';
    /**
     * @var TagService $service
     */
    protected $service;

    /**
     * TagController constructor.
     * @param TagRepository $repository
     * @param TagService $service
     */
    public function __construct(
        TagRepository $repository,
        TagService $service
    ){
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
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
     * Create a new tag
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
     * Update a tag
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
     * Show item
     *
     * @param $locale
     * @param $slug
     * @return JsonResponse
     */
    public function show($locale, $id)
    {
        if (Language::where('short_name', '=', $locale)->first()) {
            app()->setLocale($locale);
        }

        $model = $this->repository->find($id);
        $data = $this->successResponse($this->modelName, $model);
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
