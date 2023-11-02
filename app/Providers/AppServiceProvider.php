<?php

namespace App\Providers;

use App\Http\Controllers\Admin\ShopController;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\ShopGroup;
use App\Models\Structure;
use Illuminate\Support\Facades\Auth;

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

        Paginator::defaultView('vendor.pagination.bootstrap-5');

        View::share([
            'ShopGroups' => \App\Http\Controllers\ShopController::buildGroupTree(),
            'TopMenuStructures' => Structure::where("active", 1)->where("parent_id", 0)->where("structure_menu_id", 1)->orderBy("sorting")->get(),
        ]);
    }
}
