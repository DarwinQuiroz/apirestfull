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
Route::apiResource('categories', 'Api\CAtegoryController');
Route::apiResource('produtcs', 'Api\ProductController', ['only' => ['index', 'show']]);
Route::apiResource('transactions', 'Api\TransactionController', ['only' => ['index', 'show']]);
Route::apiResource('sellers', 'Api\SellerController', ['only' => ['index', 'show']]);
Route::apiResource('users', 'Api\UserController');