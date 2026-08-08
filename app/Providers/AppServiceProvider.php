<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Interfaces\MedicineCompanyRepository;
use App\Domain\Interfaces\MedicineRepository;
use App\Domain\Interfaces\UserRepository;
use App\Infrastructure\Persistence\Repositories\MedicineCompanyRepositoryImpl;
use App\Infrastructure\Persistence\Repositories\MedicineRepositoryImpl;
use App\Infrastructure\Persistence\Repositories\UserRepositoryImpl;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepository::class,
            UserRepositoryImpl::class
        );

        $this->app->bind(
            MedicineCompanyRepository::class,
            MedicineCompanyRepositoryImpl::class
        );

        $this->app->bind(
            MedicineRepository::class,
            MedicineRepositoryImpl::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}