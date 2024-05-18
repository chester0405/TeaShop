<?php

use App\Http\Controllers\api\backweb\backProductClass\productclassController; //後台商品分類
use App\Http\Controllers\api\backweb\backProduct\productController; //後台商品
use App\Http\Controllers\api\backweb\order\orderController as bcms_orderController;

use App\Http\Controllers\api\web\login\loginController;
use App\Http\Controllers\api\web\productView\productViewController; //前台商品
use App\Http\Controllers\api\web\productclassView\productclassViewController; //前台商品分類

use App\Http\Controllers\api\web\cart\cartController;

use App\Http\Controllers\api\web\member\orderController;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => ['Cors']], function (){

    // 會員
    Route::post('web/register', 'App\Http\Controllers\api\web\register\registerController@register');

    Route::post('web/login', 'App\Http\Controllers\api\web\login\loginController@login');

    Route::post('web/memberProfile', 'App\Http\Controllers\api\web\member\memberController@memberProfile');

    Route::post('web/memberProfileChange', 'App\Http\Controllers\api\web\member\memberController@memberProfileChange');

    Route::post('web/memberPasswordChange', 'App\Http\Controllers\api\web\member\memberController@memberPasswordChange');

    Route::post('web/memberPasswordChange', 'App\Http\Controllers\api\web\member\memberController@memberPasswordChange');

    Route::post('web/product/add_cart', [productViewController::class, 'addCart']);

    // 購物車
    Route::post('web/cart/list', [cartController::class, 'list']);
    Route::post('web/cart/update_product_amount', [cartController::class, 'update_product_amount']);
    Route::post('web/cart/delete', [cartController::class, 'delete']);
    Route::post('web/cart/createOrder', [cartController::class, 'createOrder']);

    //會員 訂單
    Route::post('web/member/order/list', [orderController::class, 'list']);
    Route::post('web/member/order/get', [orderController::class, 'get']);
    Route::post('web/member/order/orderComplete', [orderController::class, 'orderComplete']);

    // 後台管理員端

    Route::post('backweb/backLogin', 'App\Http\Controllers\api\backweb\backLogin\backLoginController@backLogin');

    Route::post('backweb/backShowMember', 'App\Http\Controllers\api\backweb\backLogin\backLoginController@backShowMember');

    Route::post('backweb/backLoadMember', 'App\Http\Controllers\api\backweb\backLogin\backLoginController@backLoadMember');

    Route::post('backweb/backupdateMember', 'App\Http\Controllers\api\backweb\backLogin\backLoginController@backupdateMember');

    Route::post('backweb/backupdateMember', 'App\Http\Controllers\api\backweb\backLogin\backLoginController@backupdateMember');

    Route::post('backweb/backdeleteMember', 'App\Http\Controllers\api\backweb\backLogin\backLoginController@backdeleteMember');

    // 商品分類(管理員)

    Route::post('backweb/productclassInsert',[productclassController::class,'productclassInsert']);

    Route::post('backweb/productclassView',[productclassController::class,'productclassView']);

    Route::post('backweb/productclassEdit',[productclassController::class,'productclassEdit']);

    Route::post('backweb/productclassDelete',[productclassController::class,'productclassDelete']);

    // 商品(管理員)

    Route::post('backweb/productInsert',[productController::class,'productInsert']);

    Route::post('backweb/productView',[productController::class,'productView']);

    Route::post('backweb/productEdit',[productController::class,'productEdit']);

    Route::post('backweb/productDelete',[productController::class,'productDelete']);

    Route::post('backweb/uploadImage',[productController::class,'uploadImage']);

    // 茶葉品種(管理員)

    Route::post('backweb/producttypeInsert',[productclassController::class,'producttypeInsert']);

    Route::post('backweb/producttypeView',[productclassController::class,'producttypeView']);

    Route::post('backweb/producttypeEdit',[productclassController::class,'producttypeEdit']);

    Route::post('backweb/producttypeDelete',[productclassController::class,'producttypeDelete']);

    //後台訂單
    Route::post('backweb/order/list',[bcms_orderController::class,'list']);
    Route::post('backweb/order/get',[bcms_orderController::class,'get']);
    Route::post('backweb/order/send_succeful',[bcms_orderController::class,'send_succeful']);

    // 商品分類(客戶)
    Route::post('web/productViewFront',[productViewController::class,'productViewFront']);



    // 商品茶種(客戶)
    Route::post('web/productclassViewFront',[productclassViewController::class,'productclassViewFront']);

    Route::post('web/productInfo',[productclassViewController::class,'productInfo']);

    Route::get('web/productHot',[productclassViewController::class,'productHot']);

    

});


