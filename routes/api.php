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

Route::get('/launcher/ping',[App\Http\Controllers\LauncherController::class,'Ping']);

/*Система скинов */
/*Получить скин игрока */
Route::get('/launcher/skin/{playername}.png',[App\Http\Controllers\SkinController::class,'FetchSkin']);
/*Самостоятельная установка скина самому себе */


/*Авторизация */
/*OAuth провайдер для авторизации по гуглу, вк, яндексу и т.п. */
Route::get('/launcher/auth/OAuth',[App\Http\Controllers\UserController::class,'OAuth']);

/*Регистрация локальная */
Route::post('/launcher/auth/localRegister',[App\Http\Controllers\UserController::class,'Register']);
/*Авторизация по локальным параметрам */
Route::post('/launcher/auth/localLogin',[App\Http\Controllers\UserController::class,'Login']);
/*Получить данные пользователя по сессии */
Route::post('/launcher/auth/FetchUserDataByAuth',[App\Http\Controllers\UserController::class,'FetchUserDataByAuth']);
/*Получить данные пользователя по видимым данным  ? 'id','playername','email' */
Route::post('/launcher/auth/FetchUserDataByVisibleData',[App\Http\Controllers\UserController::class,'FetchUserDataByVisibleData']);
/* KeepAlive - проверка валидности сессии пользователя*/
Route::post('/launcher/auth/KeepAlive',[App\Http\Controllers\UserController::class,'KeepAlive']);
