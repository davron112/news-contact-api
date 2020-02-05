<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepository;
use App\Services\Contracts\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;

class CategoriesController extends Controller
{
    /**
     * @var CategoryRepository $repository
     */
    protected $repository;

    /**
     * @var string $modelName
     */
    private $modelName = 'Category';

    /**
     * @var string $modelNameMultiple
     */
    private $modelNameMultiple = 'Categories';
    /**
     * @var CategoryService $service
     */
    protected $service;

    /**
     * CategoryController constructor.
     * @param CategoryRepository $repository
     * @param CategoryService $service
     */
    public function __construct(
        CategoryRepository $repository,
        CategoryService $service
    ){
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Show Categories.
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
     * Show Categories.
     *
     * @return JsonResponse
     */
    public function menu()
    {
        $categories = $this->repository->all();

        $response = [];
        $res = [];
        foreach ($categories as $key => $category) {
                if ($category->parent_id) {
                    $response['name'] = $category->name;
                    $response['slug'] = $category->slug;
                    $response['children'] = Category::where('id', $category->parent_id)->get();
                    $langAll = [];
                    foreach ($category->translations as $translate) {
                        $langAll[$translate->language->short_name] = ["name" => $translate->name];
                    }
                    $response['translations'] = $langAll;
                    $res [] = $response;
                }
        }
        return response(
            $this->successResponse(
                'menu',
                $res
            )
        );
    }

    /**
     * Create a new category
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
     * Update a category
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
     * Show category
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
