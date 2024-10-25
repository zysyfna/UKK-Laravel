<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\detail_transaksiController;
use App\Models\detail_transaksiModel;

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
//meja
Route::post('/addMeja',[MejaController::class,'addMeja']);
Route::get('/getMeja',[MejaController::class,'getMeja']);
Route::put('/updateMeja/{id_meja}',[MejaController::class,'updateMeja']);
Route::delete('/deleteMeja/{id_meja}',[MejaController::class,'deleteMeja']);

//user
Route::post('/addUser',[UserController::class,'addUser']);
Route::get('/getUser',[UserController::class,'getUser']);
Route::put('/updateUser/{id_user}',[UserController::class,'updateUser']);
Route::delete('/deleteUser/{id_user}',[UserController::class,'deleteUser']);

//menu
Route::post('/addMenu',[MenuController::class,'addMenu']);
Route::get('/getMenu',[MenuController::class,'getMenu']);
Route::put('/updateMenu/{id}',[MenuController::class,'updateMenu']);
Route::delete('/deleteMenu/{id}',[MenuController::class,'deleteMenu']);

//transaksi
Route::post('/addTransaksi',[TransaksiController::class,'addTransaksi']);
Route::get('/getTransaksi',[TransaksiController::class,'getTransaksi']);
Route::put('/updateTransaksi/{id}',[TransaksiController::class,'updateTransaksi']);
Route::delete('/deleteTransaksi/{id}',[TransaksiController::class,'deleteTransaksi']);

//Detail Transaksi
Route::get('/getAll',[detail_transaksiController::class,'getAll']);
Route::post('/addDetailTransaksi/{id}',[detail_transaksiController::class,'addDetailTransaksi']);
Route::put('/updateDetailTransaksi/{id}',[detail_transaksiController::class,'updateDetailTransaksi']);
Route::delete('/deleteDetailTransaksi/{id}',[detail_transaksiController::class,'deleteDetailTransaksi']);

//Auth
Route::post('/register',[AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/refresh', [AuthController::class,'refresh']);
Route::post('/logout', [AuthController::class,'logout']);
