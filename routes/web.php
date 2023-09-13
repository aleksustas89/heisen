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

    //ajax
    Route::post('/get-modification', [App\Http\Controllers\ShopItemController::class, 'getModification']);
}

// Route::group(['namespace' => 'App\Http\Controllers'], function() {

//     Route::view('user/login', 'user.login');
//     Route::post('user/login', 'Auth\LoginController@login');

//     Route::get('user/account', 'Auth\ChangeController@show');
//     Route::post('user/account', 'Auth\ChangeController@execute')->name('changeUser');

// });

//главная страница
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);









//Route::get('/migrate/groups', [App\Http\Controllers\MigrateController::class, 'groups'])->name('migrate-groups');
//Route::get('/migrate/items', [App\Http\Controllers\MigrateController::class, 'items'])->name('migrate-items');
//Route::get('/migrate/groups/images', [App\Http\Controllers\MigrateController::class, 'groupImages'])->name('migrate-images');
//Route::get('/migrate/items/images', [App\Http\Controllers\MigrateController::class, 'itemImages'])->name('migrate-item-images');
//Route::get('/migrate/mod/images', [App\Http\Controllers\MigrateController::class, 'modImages'])->name('migrate-mod-images');
//Route::get('/migrate/mod/additional-images', [App\Http\Controllers\MigrateController::class, 'additionalImages'])->name('migrate-add-images');
//Route::get('/migrate/list-items', [App\Http\Controllers\MigrateController::class, 'listItems'])->name('migrate-list-items');
//Route::get('/migrate/properties', [App\Http\Controllers\MigrateController::class, 'properties'])->name('migrate-properties');
//Route::get('/migrate/properties-for-groups', [App\Http\Controllers\MigrateController::class, 'propertiesForGroups'])->name('migrate-properties-for-groups');
//Route::get('/migrate/property_value_ints', [App\Http\Controllers\MigrateController::class, 'propertyValueInts'])->name('migrate-property_value_ints');
//Route::get('/migrate/property_value_strings', [App\Http\Controllers\MigrateController::class, 'propertyValueStrings'])->name('migrate-property_value_strings');
//Route::get('/migrate/property_value_floats', [App\Http\Controllers\MigrateController::class, 'propertyValueFloat'])->name('migrate-property_value_float');
//Route::get('/migrate/mod-names', [App\Http\Controllers\MigrateController::class, 'modNames'])->name('migrate-mod-names');
//Route::get('/migrate/item-names', [App\Http\Controllers\MigrateController::class, 'itemNames'])->name('migrate-item-names');
//Route::get('/migrate/group-names', [App\Http\Controllers\MigrateController::class, 'groupNames'])->name('migrate-group-names');
//Route::get('/migrate/normalize-values', [App\Http\Controllers\MigrateController::class, 'normalizeValues'])->name('migratenormalizeValues');
//Route::get('/migrate/currensies', [App\Http\Controllers\MigrateController::class, 'currensies'])->name('migratecurrensies');