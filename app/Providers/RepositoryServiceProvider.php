<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepository as UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // User
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // Bind extended repositories. The method must be called after all binds.
        $this->bindExtended();
    }

    /**
     * Bind extended repositories.
     */
    protected function bindExtended(): void
    {
    }
}
