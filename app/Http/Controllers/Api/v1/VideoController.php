<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Repositories\Contracts\VideoRepository;
use App\Services\Contracts\VideoService;
use Illuminate\Http\JsonResponse;

/**
 * Class VideoController
 * @package App\Http\Controllers\Api\v1
 */
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
        $model = $this->repository->find($id);
        $data = $this->successResponse($this->modelName, $model);
        return response()->json($data, $data['code']);
    }

}
