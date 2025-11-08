<?php

namespace App\Providers;
use App\Interfaces\AdminInterface;
use App\Repositories\AdminRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->bind(AdminInterface::class, AdminRepository::class);
       
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
