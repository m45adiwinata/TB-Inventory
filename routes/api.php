<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SubKategoriController;
use App\Http\Controllers\SegmenController;
use App\Http\Controllers\SubSegmenController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(DivisionController::class)->prefix('master/division')->group(function () {
    Route::get('', 'index');
    Route::post('', 'save');
    Route::put('update/{code}', 'update');
    Route::delete('delete/{code}', 'delete');
});

Route::controller(KategoriController::class)->prefix('master/kategori')->group(function () {
    Route::get('', 'index');
    Route::post('', 'save');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
});

Route::controller(SubKategoriController::class)->prefix('master/sub-kategori')->group(function () {
    Route::get('', 'index');
    Route::get('view/{id}', 'view');
    Route::post('', 'save');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
});

Route::controller(SegmenController::class)->prefix('master/segmen')->group(function () {
    Route::get('', 'index');
    Route::get('view/{id}', 'view');
    Route::post('', 'save');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
});

Route::controller(SubSegmenController::class)->prefix('master/sub-segmen')->group(function () {
    Route::get('', 'index');
    Route::get('view/{id}', 'view');
    Route::post('', 'save');
    Route::put('update/{id}', 'update');
    Route::delete('delete/{id}', 'delete');
});