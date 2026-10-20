<?php

namespace App\Providers;

use App\Domain\Interfaces\AmbulanceRepositoryInterface;
use App\Domain\Interfaces\BloodDonationRepositoryInterface;
use App\Domain\Interfaces\MedicineCompanyRepository;
use App\Domain\Interfaces\MedicineRepository;
use App\Domain\Interfaces\StockRepositoryInterface;
use App\Domain\Interfaces\StoreProductRepositoryInterface;
use App\Domain\Interfaces\StoreRepositoryInterface;
use App\Domain\Interfaces\UserRepository;
use App\Infrastructure\Persistence\Repositories\AmbulanceRepository;
use App\Infrastructure\Persistence\Repositories\BloodDonationRepository;
use App\Infrastructure\Persistence\Repositories\MedicineCompanyRepositoryImpl;
use App\Infrastructure\Persistence\Repositories\MedicineRepositoryImpl;
use App\Infrastructure\Persistence\Repositories\StockRepository;
use App\Infrastructure\Persistence\Repositories\StoreProductRepository;
use App\Infrastructure\Persistence\Repositories\StoreRepository;
use App\Infrastructure\Persistence\Repositories\UserRepositoryImpl;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(StoreProductRepositoryInterface::class, StoreProductRepository::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
