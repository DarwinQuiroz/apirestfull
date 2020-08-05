<?php

use Illuminate\Http\Request;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('buyers', 'Api\BuyerController', ['only' => ['index', 'show']]);
Route::apiResource('buyers.transactions', 'Api\BuyerTransactionController', ['only' => ['index']]);
Route::apiResource('buyers.products', 'Api\BuyerProductController', ['only' => ['index']]);
Route::apiResource('buyers.sellers', 'Api\BuyerSellerController', ['only' => ['index']]);
Route::apiResource('buyers.categories', 'Api\BuyerCategoryController', ['only' => ['index']]);

Route::apiResource('categories', 'Api\CategoryController');
Route::apiResource('categories.products', 'Api\CategoryProductController', ['only' => ['index']]);
Route::apiResource('categories.sellers', 'Api\CategorySellerController', ['only' => ['index']]);
Route::apiResource('categories.transactions', 'Api\CategoryTransactionController', ['only' => ['index']]);
Route::apiResource('categories.buyers', 'Api\CategoryBuyerController', ['only' => ['index']]);

Route::apiResource('products', 'Api\ProductController', ['only' => ['index', 'show']]);
Route::apiResource('products.transactions', 'Api\ProductTransactionController', ['only' => ['index']]);
Route::apiResource('products.buyers', 'Api\ProductBuyerController', ['only' => ['index']]);
Route::apiResource('products.categories', 'Api\ProductCategoryController', ['only' => ['index', 'update', 'destroy']]);
Route::apiResource('products.buyers.transactions', 'Api\ProductBuyerTransactionController', ['only' => ['store']]);

Route::apiResource('transactions', 'Api\TransactionController', ['only' => ['index', 'show']]);
Route::apiResource('transactions.categories', 'Api\TransactionCategoryController', ['only' => ['index']]);
Route::apiResource('transactions.sellers', 'Api\TransactionSellerController', ['only' => ['index']]);

Route::apiResource('sellers', 'Api\SellerController', ['only' => ['index', 'show']]);
Route::apiResource('sellers.transactions', 'Api\SellerTransactionController', ['only' => ['index']]);
Route::apiResource('sellers.categories', 'Api\SellerCategoryController', ['only' => ['index']]);
Route::apiResource('sellers.buyers', 'Api\SellerBuyerController', ['only' => ['index']]);
Route::apiResource('sellers.products', 'Api\SellerProductController', ['except' => ['show']]);

Route::get('users/me', 'Api\UserController@me')->name('me');
Route::apiResource('users', 'Api\UserController');
Route::get('users/verify/{token}', 'Api\UserController@verify')->name('verify');
Route::get('users/{user}/resend', 'Api\UserController@resend')->name('resend');

Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
