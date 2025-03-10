<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\InformationsystemItem;
use App\Models\Structure;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use App\Observers\StructureObserver;
use App\Observers\ShopGroupObserver;
use App\Observers\ShopItemObserver;
use App\Observers\InformationsystemItemObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Structure::observe(new StructureObserver);
        ShopGroup::observe(new ShopGroupObserver);
        ShopItem::observe(new ShopItemObserver);
        InformationsystemItem::observe(new InformationsystemItemObserver);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
