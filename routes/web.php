<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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

// Route::get('/', function () {
//     return view('welcome');
// });

//Auth::routes();

/* админка */
Route::group(['middleware' => ['auth', 'authForceLogoutUnActive',], 'namespace' => 'App\Http\Controllers\Admin'], function() {
    
    Route::middleware(['role:admin'])->prefix("admin")->group(function() {
        Route::get('/', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('homeAdmin');
        Route::resource('structure', StructureController::class);
        Route::resource('client', ClientController::class);
        Route::resource('user', UserController::class);
        Route::resource('shop', ShopController::class);
        Route::resource('shopGroup', ShopGroupController::class);
        //удаление картинок
        Route::get('/shopGroup/{id}/delete/{field}', [ShopGroupController::class, 'deleteImage']);
        Route::resource('shopCurrency', ShopCurrencyController::class);
        Route::resource('shopItem', ShopItemController::class);
        Route::get('/shop/shopItem/{shopItem}/copy', [App\Http\Controllers\Admin\ShopItemController::class, 'copy'])->name("copyShopItem");
        
        Route::get('/deleteShopItemImage/{id}', [App\Http\Controllers\Admin\ShopItemController::class, 'deleteImage']);
        Route::get('/sortShopItemImages', [App\Http\Controllers\Admin\ShopItemController::class, 'sortShopItemImages']);
        Route::resource('shopItemProperty', ShopItemPropertyController::class);
        //удаление свойств
        Route::get('/deleteShopItemPropertyValue/{property}/{id}', [App\Http\Controllers\Admin\ShopItemController::class, 'deletePropertyValue']);
        Route::resource('shopItemList', ShopItemListController::class);
        Route::resource('shopItemListItem', ShopItemListItemController::class);
        Route::resource('structureMenu', StructureMenuController::class);
        Route::resource('shopOrder', ShopOrderController::class);
        Route::post('/shop/order/cdek/create/order', [App\Http\Controllers\CdekController::class, 'cdekCreateOrder'])->name("createCdekOrder");
        Route::get('/shop/order/cdek/create/print/{CdekOrder}', [App\Http\Controllers\CdekController::class, 'print'])->name("printCdekOrder");

        Route::resource('shopQuickOrder', ShopQuickOrderController::class);
        Route::resource('shopOrderItem', ShopOrderItemController::class);
        Route::resource('shopDelivery', ShopDeliveryController::class);
        Route::resource('shopDeliveryField', ShopDeliveryFieldController::class);
        Route::resource('modification', ModificationController::class);
        Route::get('/default_modification', [App\Http\Controllers\Admin\ModificationController::class, 'defaultModification'])->name("modByDefault");

        Route::resource('shopDiscount', ShopDiscountController::class);
        Route::get('/list/values', [App\Http\Controllers\Admin\ShopDiscountController::class, 'listValues']);
        Route::get('/shop/discount/filter', [App\Http\Controllers\Admin\ShopDiscountController::class, 'filter']);
        Route::resource('shopItemDiscount', ShopItemDiscountController::class);
        Route::resource('comment', CommentController::class);
        Route::resource('cdekSender', CdekSenderController::class);

        Route::resource('shop.shop-price', App\Http\Controllers\Admin\ShopPriceController::class)->only(['index', 'update']);

        Route::prefix("search")->group(function() {
            Route::get('/', [App\Http\Controllers\Admin\SearchController::class, 'index'])->name("adminSearch");
            Route::get('/indexing', [App\Http\Controllers\Admin\SearchController::class, 'indexing'])->name("adminSearchIndexing");
        });
        
   
        //editable-fields
        Route::get('/editable/', [App\Http\Controllers\Admin\EditableController::class, 'query']);
        Route::get('/toggle/', [App\Http\Controllers\Admin\ToggleController::class, 'query']);

        Route::post('logout', 'App\Http\Controllers\Admin\LoginController@logout')->name('logout');
    });
});
/*если админ не авторизован*/
Route::get('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'index'])->name('admin_login_form');
Route::post('/admin/login', [App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin_login_action');


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


$sCurrentPath = request()->path();

if (Schema::hasTable('structures')) {

    if ($Structure = App\Http\Controllers\StructureController::getStructure()) {
        App\Http\Controllers\StructureController::show($sCurrentPath, $Structure);
    }
}

if (Schema::hasTable('shops')) {
 
    if ($shopObject = App\Http\Controllers\ShopController::getObjectByPath()) {
        $object_name = get_class($shopObject);

        if ($object_name == 'App\Models\ShopGroup') {
            App\Http\Controllers\ShopGroupController::show($sCurrentPath, $shopObject);
        } else if ($object_name == 'App\Models\ShopItem') {
            App\Http\Controllers\ShopItemController::show($sCurrentPath, $shopObject);
        }
    }
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
