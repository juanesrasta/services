<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SubcategoryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/** JUAN
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::controller(CategoryController::class)->group(function () {
    Route::get('categories', 'index');
    Route::post('category', 'store');
	Route::get('category/{id}', 'show');
	Route::put('category/{id}', 'update');
	Route::post('categorystt', 'categorystt');
    Route::delete('category/{id}', 'destroy');
});

Route::controller(SubcategoryController::class)->group(function () {
    Route::get('subcategories', 'index');
    Route::post('subcategory', 'store');
    Route::post('subcatofcat', 'subCatOfCat');
	Route::get('subcategory/{id}', 'show');
	Route::put('subcategory/{id}', 'update');
	Route::post('subcategorystt', 'subcategorystt');
    Route::delete('subcategory/{id}', 'destroy');
});

Route::controller(UserController::class)->group(function () {
    Route::get('user', 'index');
    Route::post('user', 'store');
	Route::get('user/{id}', 'show');
	Route::put('user/{id}', 'update');
	Route::post('userstt', 'userstt');
    Route::delete('user/{id}', 'destroy');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'index');
    Route::post('product', 'store');
	Route::get('product/{id}', 'show');
	Route::put('product/{id}', 'update');
    Route::delete('product/{id}', 'destroy');
});