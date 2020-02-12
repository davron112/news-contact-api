<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\Contracts\ArticleRepository;
use App\Services\Contracts\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class ArticlesController extends Controller
{
    /**
     * @var ArticleRepository $repository
     */
    protected $repository;

    /**
     * @var string $modelName
     */
    private $modelName = 'Article';

    /**
     * @var string $modelNameMultiple
     */
    private $modelNameMultiple = 'Articles';
    /**
     * @var ArticleService $service
     */
    protected $service;

    /**
     * ArticlesController constructor.
     * @param ArticleRepository $repository
     * @param ArticleService $service
     */
    public function __construct(
        ArticleRepository $repository,
        ArticleService $service
    ){
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Show Articles.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $articles = $this->repository
            ->orderBy('created_at','DESC')
            ->all();

        $articles->map(function (Article $article) {
            $article->addVisible(
                'translations', 'category_id', 'content', 'status');
        });
        return response(
            $this->successResponse(
                $this->modelNameMultiple,
                $articles
            )
        );
    }

    /**
     * Show latest Articles.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function latest(Request $request)
    {
        $data = $request->all();
        $limit = $request->has('limit') ? array_get($data, 'limit') : 9;
        return response(
            $this->successResponse(
                $this->modelNameMultiple,
                $this->repository
                    ->orderBy('created_at','DESC')
                    ->paginate($limit)
            )
        );
    }

    /**
     * Create a new article
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
     * Update a article
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
     * @param $slug
     * @return JsonResponse
     */
    public function show($slug)
    {
        if (is_numeric($slug)) {
            $article = $this->repository->find($slug);
        } else {
            $article = $this->repository->findWhere(['slug' => $slug])->first();
        }
        $article->addVisible('category_id', 'content', 'status', 'translations');
        $data = $this->successResponse($this->modelName, $article);
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
