<?php

namespace App\Providers;

use App\Repositories\Abstracts\CarRepositoryInterface;
use App\Repositories\Abstracts\BrandRepositoryInterface;
use App\Repositories\Concretes\BrandRepository;
use App\Repositories\Concretes\CarRepository;
use Illuminate\Support\ServiceProvider;

class ApiRepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->bind(CarRepositoryInterface::class, CarRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() : void
    {
        //
    }
}
