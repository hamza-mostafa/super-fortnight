<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\JWTController;
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

Route::post('/register', [JWTController::class, 'register']);
Route::post('/login', [JWTController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function($router) {
    Route::post('/refresh', [JWTController::class, 'refresh']);
    Route::post('/profile', [JWTController::class, 'profile']);
    Route::post('/brands/fetchByCarModelOrBrand', [BrandController::class, 'fetchByCarModelOrBrand']);
    Route::apiResources([
        'cars'=> CarController::class,
        'brands'=> BrandController::class,
    ]);
});

Route::fallback(function(){
    return response()->json([
        'data' => [
            'message' => 'Page Not Found. If error persists, contact info@website.com']
    ], 404);
});
