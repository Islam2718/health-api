<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Interfaces\MedicineCompanyRepository;
use App\Domain\Interfaces\MedicineRepository;
use App\Domain\Interfaces\UserRepository;
use App\Infrastructure\Persistence\Repositories\MedicineCompanyRepositoryImpl;
use App\Infrastructure\Persistence\Repositories\MedicineRepositoryImpl;
use App\Infrastructure\Persistence\Repositories\UserRepositoryImpl;
use App\Domain\Interfaces\BloodDonationRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\BloodDonationRepository;
use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\AmbulanceRepository;

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

        $this->app->bind(
            BloodDonationRepositoryInterface::class,
            BloodDonationRepository::class
        );
        $this->app->bind(
            AmbulanceRepositoryInterface::class,
            AmbulanceRepository::class
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
