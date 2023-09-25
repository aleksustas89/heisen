<?php

use App\Models\ShopItem;
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

Route::get('/', function () {
    return view('welcome');
});

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
        Route::get('/deleteShopItemImage/{id}', [App\Http\Controllers\Admin\ShopItemController::class, 'deleteImage']);
        Route::resource('shopItemProperty', ShopItemPropertyController::class);
        //удаление свойств
        Route::get('/deleteShopItemPropertyValue/{property}/{id}', [App\Http\Controllers\Admin\ShopItemController::class, 'deletePropertyValue']);
        Route::resource('shopItemList', ShopItemListController::class);
        Route::resource('shopItemListItem', ShopItemListItemController::class);
        Route::resource('structureMenu', StructureMenuController::class);
        Route::resource('shopOrder', ShopOrderController::class);
        Route::resource('shopQuickOrder', shopQuickOrderController::class);
        Route::resource('shopOrderItem', ShopOrderItemController::class);
        Route::resource('shopDelivery', ShopDeliveryController::class);
        Route::resource('shopDeliveryField', ShopDeliveryFieldController::class);
        Route::resource('modification', ModificationController::class);
        Route::resource('shopDiscount', ShopDiscountController::class);
        Route::resource('shopItemDiscount', ShopItemDiscountController::class);

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

    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name("cartIndex");
    Route::post('/cart', [App\Http\Controllers\CartController::class, 'saveOrder'])->name("saveIndex");
    Route::post('/add-to-cart', [App\Http\Controllers\CartController::class, 'addToCart'])->name("cartAdd");
    Route::post('/get-cart', [App\Http\Controllers\CartController::class, 'getLittleCart'])->name("getCart");
    Route::post('/delete-from-cart', [App\Http\Controllers\CartController::class, 'deleteFromCart'])->name("deleteFromCart");
    Route::post('/get-modification', [App\Http\Controllers\ShopItemController::class, 'getModification']);
    Route::get('/get-cities', [App\Http\Controllers\CartController::class, 'getCities']);
    Route::post('/shop-quich-order', [App\Http\Controllers\ShopQuickOrderController::class, 'save']);
}

Route::group(['namespace' => 'App\Http\Controllers'], function() {

    Route::prefix("client")->group(function() {

        Route::group(['middleware' => ['client-auth']], function() {
            Route::get('login', 'Auth\LoginController@showLoginForm')->name("loginForm");
            Route::post('login', 'Auth\LoginController@login')->name("login");
            Route::get('register', 'Auth\RegisterController@registerForm')->name("registerForm");
            Route::post('register', 'Auth\RegisterController@register')->name("register");
        });

        Route::group(['middleware' => ['client']], function() {
            Route::get('account', 'Auth\ClientController@show')->name("clientAccount");
            Route::post('account', 'Auth\ClientController@execute')->name('clientUpdate');
            Route::post('logout', 'Auth\ClientController@logout')->name('clientLogout');

            Route::get('orders', 'Auth\ClientController@orders')->name("clientOrders");

            Route::prefix("favorites")->group(function() {
                Route::get('/', 'Auth\ClientController@favorites')->name("clientFavorites");
                Route::post('add', 'Auth\ClientController@addFavorite')->name("addFavorite");
            });

        });

    });


    Route::prefix("search")->group(function() {
        Route::get('/', [App\Http\Controllers\SearchController::class, 'show'])->name("search");
        Route::get('/autocomplete', [App\Http\Controllers\SearchController::class, 'Autocomplete'])->name("search-autocomplete");
    });
    

});

//главная страница
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name("home");