<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Repositories\Contracts\ArticleRepository;
use App\Services\Contracts\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $data = $request->all();
        $language = array_get($data, 'language');
        if ($language) {
            app()->setLocale($language);
        }

        $categorySlug = array_get($data, 'category_slug');
        $articles = $this->repository->orderBy('created_at','DESC');
        $categoryId = false;

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->get()->first();
            if ($category) {
                $categoryId = $category->id;
                $articles = $articles->where('category_id', $categoryId);
            }
        }

        if (!$articles->count()) {
            return response(
                $this->successResponse(
                    $this->modelNameMultiple,
                    []
                )
            );
        }
        $limit = array_get($data, 'limit', 9);

        $limit = $articles->count() < $limit ? $articles->count() : $limit;

        $articles = $articles->paginate($limit);

        $currentPage = $articles->currentPage();


        if ($currentPage == 1) {
            if ($categoryId) {
                $bannerArticle = $this->repository->findWhere(['category_id' => $categoryId, 'is_main' => 1])->first()
                    ? $this->repository->findWhere(['category_id' => $categoryId, 'is_main' => 1])->first()->toArray()
                    : $this->repository->findWhere(['category_id' => $categoryId])->first()->toArray();
                $articles = $articles->toArray();
                $dataFiltered = [];
                foreach ($articles['data'] as $key => $value) {

                    if ($value['id'] == array_get($bannerArticle, 'id')) {
                        continue;
                    } else {
                        $dataFiltered[$key] = $value;
                    }
                }
                $articles['data'] = $dataFiltered;
                if ($bannerArticle) {
                    array_unshift($articles['data'], $bannerArticle);
                }
            } else {
                $bannerArticle = $this->repository->findWhere(['is_main' => 1])->first()
                    ? $this->repository->findWhere(['is_main' => 1])->first()->toArray()
                    : $this->repository->first()->toArray();

                $articles = $articles->toArray();
                $dataFiltered = [];
                foreach ($articles['data'] as $key => $value) {
                    if ($value['id'] == array_get($bannerArticle, 'id')) {
                        continue;
                    } else {
                        $dataFiltered[$key] = $value;
                    }
                }
                $articles['data'] = $dataFiltered;
                if ($bannerArticle) {
                    array_unshift($articles['data'], $bannerArticle);
                }
            }


        }

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
        $language = array_get($data, 'language');
        if ($language) {
            app()->setLocale($language);
        }
        $limit = array_get($data, 'limit', 14);
        $articles = $this->repository->orderBy('created_at','DESC');
        $articles = $articles->paginate($limit);
        $articles = $articles->toArray();

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
    public function alike(Request $request, $lang, $slug)
    {
        $data = $request->all();
        $language = $lang ?? array_get($data, 'language', 'uz');
        if ($language) {
            app()->setLocale($language);
        }
        $article = $this->repository->with('tags')->findWhere(['slug' => $slug])->first();
        $tag = $article->tags()->first();

        $limit = array_get($data, 'limit', 14);
        $articles = $tag->articles()->where('id', '!=', $article->id);

        $articles = $articles->paginate($limit);
        $articles = $articles->toArray();

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
    public function search(Request $request)
    {
        $data = $request->all();
        $language = array_get($data, 'language');
        $q = array_get($data, 'q');
        if ($language) {
            app()->setLocale($language);
        }
        $limit = array_get($data, 'limit', 14);
        $articles = $this->repository
            ->leftjoin('article_translations', 'articles.id', '=', 'article_translations.item_id')
            ->where('article_translations.title', 'LIKE', "%{$q}%")
            ->orWhere('article_translations.description', 'LIKE', "%{$q}%")
            ->orWhere('article_translations.content', 'LIKE', "%{$q}%");

        $articles = $articles->paginate($limit);
        $articles = $articles->toArray();

        return response(
            $this->successResponse(
                $this->modelNameMultiple,
                $articles
            )
        );
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
            $article = $this->repository->with('tags')->find($slug);
        } else {
            $article = $this->repository->with('tags')->findWhere(['slug' => $slug])->first();
        }
        $article->addVisible('category_id', 'content', 'status');
        $data = $this->successResponse($this->modelName, $article);
        return response()->json($data, $data['code']);
    }

}
