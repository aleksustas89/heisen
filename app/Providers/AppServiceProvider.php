<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\ShopCurrencyController;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
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

        App::setLocale('ru');

        Paginator::defaultView('vendor.pagination.bootstrap-5');

        View::share([
            'Currency' => ShopCurrencyController::getCurrent(),
            'ShopGroups' => \App\Http\Controllers\ShopController::buildGroupTree(),
            'TopMenuStructures' => \App\Http\Controllers\StructureController::buildStructureTree(1),
        ]);
    }
}
