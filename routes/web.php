<?php

use App\Models\ShopDiscount;
use Illuminate\Support\Facades\Route;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopItem;
use Illuminate\Support\Facades\Schema;
use App\Models\Structure;


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
        Route::resource('shopOrderItem', ShopOrderItemController::class);
        Route::resource('shopDelivery', ShopDeliveryController::class);
        Route::resource('shopDeliveryField', ShopDeliveryFieldController::class);
        Route::resource('modification', ModificationController::class);
        Route::resource('shopDiscount', ShopDiscountController::class);
        Route::resource('shopItemDiscount', ShopItemDiscountController::class);
   
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
    $oStructureController = App\Http\Controllers\StructureController::class;

    foreach (Structure::where('active', '1')->get() as $structure) {
        
        if ($sPath = $structure->path([], $activeAll = false)) {

            if ($sPath == $sCurrentPath) {
                App\Http\Controllers\StructureController::show($sCurrentPath, $structure);
            }
        }
    }
}

// if (Schema::hasTable('shops')) {
//     $oShop = Shop::find(Shop::$shop_id);
//     if (!is_null($oShop->id) && $oShop->active == 1) {
//         Route::view($oShop->path, 'shop/home', ['shop' => $oShop]);

//         foreach (shopGroup::where('active', '1')->get() as $oShopGroup) {

//             if ($oShopGroup->getFullPath() == $path) {
//                 App\Http\Controllers\ShopGroupController::show($sCurrentPath, $oShopGroup);
//             }
//         }

//         foreach (shopItem::where('active', '1')->get() as $oShopItem) {

//             if ($oShopItem->url() . "/" == $path) {
//                 App\Http\Controllers\ShopItemController::show($path, $oShopItem);
//             }
//         }
//     }
// }

// Route::group(['namespace' => 'App\Http\Controllers'], function() {

//     Route::view('user/login', 'user.login');
//     Route::post('user/login', 'Auth\LoginController@login');

//     Route::get('user/account', 'Auth\ChangeController@show');
//     Route::post('user/account', 'Auth\ChangeController@execute')->name('changeUser');

// });

//главная страница
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);