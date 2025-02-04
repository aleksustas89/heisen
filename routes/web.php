<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['auth', 'authForceLogoutUnActive',], 'namespace' => 'App\Http\Controllers\Admin'], function() {
    
    Route::middleware(['role:admin'])->prefix("admin")->group(function() {

        Route::get('/', 'HomeController@index')->name('homeAdmin');
       
        Route::resources([
            'structure' => 'StructureController',
            'structure-menu' => 'StructureMenuController',
            'user' => 'UserController',
            'client' => 'ClientController',
            'shop' => 'ShopController',
            'shop.shop-group' => 'ShopGroupController',
            'shop.shop-item' => 'ShopItemController',
            'modification' => 'ModificationController',
            'shop.shop-currency' => 'ShopCurrencyController',
            'comment' => 'CommentController',
            'shop.shop-quick-order' => 'ShopQuickOrderController',
            'shop.shop-delivery' => 'ShopDeliveryController',
            'shop.shop-discount' => 'ShopDiscountController',
            'shop.shop-item-property' => 'ShopItemPropertyController',
            'shop.shop-item-list' => 'ShopItemListController',
            'shop.shop-item-list-item' => 'ShopItemListItemController',
            'shop.shop-delivery-field' => 'ShopDeliveryFieldController',
            'shop-order' => 'ShopOrderController',
            'shop-order.shop-order-item' => 'ShopOrderItemController',
            'language' => 'LanguageController',
            'shop.shop-payment-system' => 'ShopPaymentSystemController',
            'cdek-sender' => 'CdekSenderController',
            'boxberry-sender' => 'BoxberrySenderController',
            'shop.shop-filter' => 'ShopFilterController',
            'trash' => 'TrashController',
            'sitemap' => 'SitemapController',
            'statistic' => 'StatisticController'
        ]);

        Route::prefix("/statistic")->group(function() {
            Route::get('/get/group-items', 'StatisticController@getGroupItems')->name("statisticGetGroupItems");
        });
        
        Route::resource('shop.shop-price', App\Http\Controllers\Admin\ShopPriceController::class)->only(['index', 'update']);

        Route::prefix("search")->controller('SearchController')->group(function() {
            Route::get('/', 'index')->name("adminSearch");
            Route::get('/indexing', 'indexing')->name("adminSearchIndexing");
        });

        Route::prefix("shop")->group(function() {

            Route::prefix("shop-group/{shopGroup}")->group(function() {
                Route::get('/delete/{field}', 'ShopGroupController@deleteImage')->name('deleteShopGroup');
            });

            Route::get('{shopItemProperty}/delete', 'ShopItemController@deletePropertyValue')->name('deleteShopItemPropertyValue');

            Route::prefix("shop-item")->controller('ShopItemController')->group(function() {
                Route::prefix("{shopItem}")->group(function() {
                    Route::get('/copy', 'copy')->name("copyShopItem"); 
                    Route::prefix("image")->group(function() {
                        Route::get('/{shopItemImage}/delete', 'deleteImage')->name("deleteShopItemImage");
                        Route::get('/sorting', 'sortShopItemImages')->name("sortingShopItemImages");
                    });
                    
                    Route::post('image-upload', 'App\Http\Controllers\Admin\ShopItemController@uploadShopItemImage')->name("uploadShopItemImage");
                    Route::get('gallery', 'getShopItemGallery')->name("getShopItemGallery");
    
                    Route::prefix("associated")->group(function() {
                        Route::get('load-sub-groups-and-items', 'addAssociated')->name("addAssociated");
                        Route::post('save', 'saveAssociated')->name("saveAssociated");
    
                        Route::post('delete/group/{shopItemAssociatedGroup}', 'deleteShopItemAssociatedGroup')->name("deleteShopItemAssociatedGroup");
                        Route::post('delete/item/{shopItemAssociatedItem}', 'deleteShopItemAssociatedItem')->name("deleteShopItemAssociatedItem");
                        Route::post('search', 'searchShopItemFromAssosiated')->name("searchShopItemFromAssosiated");
                    });

                    Route::prefix("shortcut")->group(function() {
                        Route::get('/delete/{shopGroup}', 'ShopItemController@deleteShortcutGroup')->name("deleteShortcutGroup");
                    });

                    Route::get('/canonical', 'ShopItemController@searchCanonical')->name("SearchCanonical");
                });

                Route::prefix("shortcut")->group(function() {
                    Route::get('/get-groups', 'ShopItemController@getShortcutGroup')->name("getShortcutGroup");
                });
            });

            Route::get('/add/shortcut-from-group', 'ShopGroupController@addShortcutFromGroup')->name("addShortcutFromGroup");
            Route::post('/save/shortcut-from-group', 'ShopGroupController@saveShortcutFromGroup')->name("saveShortcutFromGroup");

            Route::get('/modification/{shopItem}/default', 'ModificationController@defaultModification')->name('defaultModification');

            Route::prefix('discount')->controller('ShopDiscountController')->group(function() {
                Route::get('/filter', 'filter')->name("shopDiscountFilter");
                Route::get('/property/values', 'listValues')->name("shopDiscountPropertyValues");
            });

            Route::prefix('order')->controller('App\Http\Controllers\Admin\ShopOrderController')->group(function() {
                Route::get('/get-clients', 'getClients')->name("getClients");
                Route::get('/get-orders', 'getOrders')->name("getOrders");

                Route::prefix('cdek')->controller('App\Http\Controllers\CdekController')->group(function() {
                    Route::post('order', [App\Http\Controllers\CdekController::class, 'cdekCreateOrder'])->name("createCdekOrder");
                    Route::get('print/{CdekOrder}', [App\Http\Controllers\CdekController::class, 'print'])->name("printCdekOrder");
                    Route::get('delete/{CdekOrder}', [App\Http\Controllers\CdekController::class, 'deleteOrder'])->name("deleteOrder");
                });

                Route::prefix('boxberry/create')->controller('App\Http\Controllers\Admin\BoxberryController')->group(function() {
                    Route::post('order', 'createOrder')->name("createBoxberryOrder");
                });

                Route::prefix('pr/create')->controller('App\Http\Controllers\Admin\PochtaRossiiController')->group(function() {
                    Route::post('order', 'createOrder')->name("createPrOrder");
                });
            });

            Route::prefix("price")->controller('ShopPriceController')->group(function() {
                Route::get('/filter', 'filter')->name("shopPriceFilter");
            });
        });

        // Route::prefix("sitemap")->controller('SitemapController')->group(function() {
        //     Route::get('/', 'index')->name("adminSitemap");
        // });
        
   
        //editable-fields
        Route::post('/editable', 'EditableController@query')->name("adminEditable");
        Route::post('/toggle', 'ToggleController@query')->name("adminToggle");
        Route::post('logout', 'LoginController@logout')->name('logout');
    });
});

Route::get('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('admin_login_form');
Route::post('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin_login_action');

Route::get('sitemap', [App\Http\Controllers\Admin\SitemapController::class, 'getSitemap'])->name("getSitemap");
Route::get('imagemap', [App\Http\Controllers\Admin\SitemapController::class, 'getImagemap'])->name("getImagemap");
Route::get('yml', [App\Http\Controllers\Admin\SitemapController::class, 'getYml'])->name("getYml");
Route::get('csv-catalog', [App\Http\Controllers\Admin\SitemapController::class, 'getCsvCatalog'])->name("getCsvCatalog");

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['namespace' => 'App\Http\Controllers'], function() {

    Route::prefix("client")->group(function() {
        Route::get('/login', 'Client\ClientLoginController@showLoginForm')->name('client.login');
        Route::post('/login', 'Client\ClientLoginController@login')->name('client.login.submit');

        Route::get('/register', 'Client\ClientRegisterController@showRegisterForm')->name('client.register');
        Route::post('/register', 'Client\ClientRegisterController@register')->name('client.register.submit');
        
        Route::get('/', 'Client\ClientLoginController@result')->name('client.result');
        Route::post('/logout', 'Client\ClientLoginController@logout')->name('client.logout');
    });
});


if (Schema::hasTable('shops')) {
 
    Route::post('/filter', [App\Http\Controllers\ShopGroupController::class, 'filter'])->name("filter");
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name("cartIndex");
    Route::post('/cart', [App\Http\Controllers\CartController::class, 'saveOrder'])->name("saveIndex");

    Route::get('/cart/payment/result', [App\Http\Controllers\CartController::class, 'finishOrder'])->name("finish-order");

    Route::post('/add-to-cart', [App\Http\Controllers\CartController::class, 'addToCart'])->name("cartAdd");
    Route::post('/update-item-in-cart', [App\Http\Controllers\CartController::class, 'updateItemInCart'])->name("updateItemInCart");
    Route::post('/get-cart', [App\Http\Controllers\CartController::class, 'getLittleCart'])->name("getCart");
    Route::post('/delete-from-cart', [App\Http\Controllers\CartController::class, 'deleteFromCart'])->name("deleteFromCart");
    Route::post('/get-modification', [App\Http\Controllers\ShopItemController::class, 'getModification']);
    Route::get('/get-cdek-cities', [App\Http\Controllers\CartController::class, 'getCdekCities']);
    Route::get('/get-cdek-offices', [App\Http\Controllers\CartController::class, 'getCdekOffices']);
    Route::post('/shop-quich-order', [App\Http\Controllers\ShopQuickOrderController::class, 'save']);
    Route::get('/shop/ajax/group/{ShopGroupId}', function ($ShopGroupId) {
        return App\Http\Controllers\ShopGroupController::getAjaxGroup($ShopGroupId);
    });
    Route::post('/save-comment', [App\Http\Controllers\ShopItemController::class, 'saveComment'])->name("saveComment");

    Route::prefix("discounts")->group(function() {
        Route::get('/', [App\Http\Controllers\ShopItemDiscountController::class, 'showItemWithDiscounts'])->name("showItemWithDiscounts");
        Route::get('/ajax', [App\Http\Controllers\ShopItemDiscountController::class, 'showItemWithDiscountsAjax'])->name("showItemWithDiscountsAjax");
    });

    Route::get('/cdek/office/choose', [App\Http\Controllers\CdekController::class, 'chooseOffice'])->name("chooseOffice");
    
}

Route::group(['namespace' => 'App\Http\Controllers'], function() {

    Route::get('favorites', 'Auth\ClientController@cookieFavorites')->name("cookieFavorites");

    Route::prefix("client")->group(function() {

        Route::group(['middleware' => ['client-auth']], function() {
            Route::get('login', 'Auth\LoginController@showLoginForm')->name("loginForm");
            Route::post('login', 'Auth\LoginController@login')->name("login");
            Route::get('register', 'Auth\RegisterController@registerForm')->name("registerForm");
            Route::post('register', 'Auth\RegisterController@register')->name("register");

            Route::controller("Auth\ResetPasswordController")->group(function() {
                Route::get('restore', 'showForm')->name("restoreForm");
                Route::post('restore', 'restore')->name("restore");
            });

        });

        Route::group(['middleware' => ['client']], function() {
            Route::get('account', 'Auth\ClientController@show')->name("clientAccount");
            Route::post('account', 'Auth\ClientController@execute')->name('clientUpdate');
            Route::post('logout', 'Auth\ClientController@logout')->name('clientLogout');

            Route::get('orders', 'Auth\ClientController@orders')->name("clientOrders");
            Route::get('favorites', 'Auth\ClientController@favorites')->name("clientFavorites");

        });

        Route::post('favorites/add', 'Auth\ClientController@addFavorite')->name("addFavorite");

    });


    Route::prefix("search")->group(function() {
        Route::get('/', [App\Http\Controllers\SearchController::class, 'show'])->name("search");
        Route::get('/autocomplete', [App\Http\Controllers\SearchController::class, 'Autocomplete'])->name("search-autocomplete");
        Route::get('/ajax', [App\Http\Controllers\SearchController::class, 'ajaxSearch']);
    });
    
    Route::post('/request-call', [App\Http\Controllers\RequestCallController::class, 'index']);

    Route::get('/comments', [App\Http\Controllers\CommentController::class, 'index'])->name("comments");

});

//главная страница
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name("home");


Route::get('/{any}', 'App\Http\Controllers\PageController@index')->where('any', '.*');