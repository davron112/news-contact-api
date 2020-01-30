<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepository as UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Contracts\UserService as UserServiceInterface;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CategoryService as CategoryServiceInterface;
use App\Repositories\Contracts\CategoryRepository as CategoryRepositoryInterface;
use App\Services\CategoryService;
use App\Repositories\CategoryRepository;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // category
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        // user
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
