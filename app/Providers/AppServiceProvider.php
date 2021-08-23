<?php

namespace App\Providers;

use App;
use App\Services\BinaryPlanService;
use App\Services\HoldingTankService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\BoomerangInv;
use App\Observers\BoomerangInvObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        App::singleton('ibu.service.binary_plan_tree', BinaryPlanService::class);
        App::singleton('ibu.service.holding_tank', HoldingTankService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('FORCE_HTTPS')) {
            URL::forceScheme('https');
        }
        BoomerangInv::observe(BoomerangInvObserver::class);
    }
}
