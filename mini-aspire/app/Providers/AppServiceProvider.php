<?php

namespace App\Providers;

use App\Http\Repositories\LoanRepository;
use App\Http\Repositories\LoanRepositoryImpl;
use App\Http\Repositories\RepaymentRepository;
use App\Http\Repositories\RepaymentRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public array $singletons = [
        LoanRepository::class => LoanRepositoryImpl::class,
        RepaymentRepository::class => RepaymentRepositoryImpl::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
