<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Repositories\Contracts\NewspaperRepository;
use App\Services\Contracts\NewspaperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class NewspaperController extends Controller
{
    /**
     * @var NewspaperRepository $repository
     */
    protected $repository;

    /**
     * @var string $modelName
     */
    private $modelName = 'Newspaper';

    /**
     * @var string $modelNameMultiple
     */
    private $modelNameMultiple = 'Newspaper';
    /**
     * @var NewspaperService $service
     */
    protected $service;

    /**
     * NewspaperController constructor.
     * @param NewspaperRepository $repository
     * @param NewspaperService $service
     */
    public function __construct(
        NewspaperRepository $repository,
        NewspaperService $service
    ){
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Show Newspaper.
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
}
