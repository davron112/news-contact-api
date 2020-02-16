<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
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
        $categories = $this->repository->orderBy('ord', 'ASC')->all();

        $response = [];
        $res = [];
        foreach ($categories as $key => $category) {
                if (!$category->parent_id) {
                    $response['name'] = $category->name;
                    $response['slug'] = $category->slug;

                    $children = [];
                    $childrenAll = [];
                    foreach ($category->children as $item) {
                        $children['name'] = $item->name;
                        $children['slug'] = $item->slug;
                        $langChildAll = [];
                        foreach ($item->translationsAll as $translateChild) {
                            $langChildAll[$translateChild->language->short_name] = [
                                "name" => $translateChild->name,
                            ];
                        }
                        $children['translations'] = $langChildAll;
                        $childrenAll[] = $children;
                    }

                    $response['children'] = $childrenAll;
                    $langAll = [];
                    foreach ($category->translationsAll as $translate) {
                        $langAll[$translate->language->short_name] = [
                            "name" => $translate->name
                        ];
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
     * Show item
     *
     * @param $locale
     * @param $slug
     * @return JsonResponse
     */
    public function show($locale, $slug)
    {
        if (Language::where('short_name', '=', $locale)->first()) {
            app()->setLocale($locale);
        }
        if (is_numeric($slug)) {
            $model = $this->repository->find($slug);
        } else {
            $model = $this->repository->findWhere(['slug' => $slug])->first();
        }

        $response = [];
        foreach ($model->toArray() as $key => $value) {

                $langAll = [];
                if ($key == 'translations') {
                    foreach ($model->translationsAll as $translate) {
                        $langAll[$translate->language->short_name] = [
                            "name" => $translate->name
                        ];
                    }
                } else {
                    $response[$key] = $value;
                }
                $response['translations'] = $langAll;
        }
        $data = $this->successResponse($this->modelName, $response);
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
