<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentManageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\OnlineMeetingController;
use App\Http\Controllers\OtherActivitiesController;
use App\Http\Controllers\TransportationController;
use App\Http\Controllers\UserController;
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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register']);

Route::prefix('password')->group(function () {
    Route::post('forgot', [AuthController::class, 'forgotPassword']);
    Route::post('token/varify', [AuthController::class, 'verifyForgotPasswordToken']);
    Route::post('reset', [AuthController::class, 'resetPassword']);
});
Route::get('email-varify', [AuthController::class, 'varifyEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth-user', [AuthController::class, 'getAuthUser']);
    Route::get('logout', [AuthController::class, 'logout']);

    Route::prefix('users')->group(function () {
        Route::get('index', [UserController::class, 'index']);
        Route::post('store', [UserController::class, 'store']);
        Route::get('show/{id}', [UserController::class, 'getSingleUser']);
        Route::patch('update', [UserController::class, 'update']);
        Route::delete('delete/{id}', [UserController::class, 'destroy']);
        Route::post('change-status', [UserController::class, 'changeStatus']);
    });

    Route::prefix('meeting-room')->group(function () {
        Route::get('index', [MeetingRoomController::class, 'index']);
        Route::post('store', [MeetingRoomController::class, 'store']);
        Route::get('show/{id}', [MeetingRoomController::class, 'getSingleData']);
        Route::patch('update', [MeetingRoomController::class, 'update']);
        Route::delete('delete/{id}', [MeetingRoomController::class, 'destroy']);
    });

    Route::prefix('transportation')->group(function () {
        Route::get('index', [TransportationController::class, 'index']);
        Route::post('store', [TransportationController::class, 'store']);
        Route::get('show/{id}', [TransportationController::class, 'getSingleData']);
        Route::patch('update', [TransportationController::class, 'update']);
        Route::delete('delete/{id}', [TransportationController::class, 'destroy']);
    });

    Route::prefix('online-meeting')->group(function () {
        Route::get('index', [OnlineMeetingController::class, 'index']);
        Route::post('store', [OnlineMeetingController::class, 'store']);
        Route::get('show/{id}', [OnlineMeetingController::class, 'getSingleData']);
        Route::patch('update', [OnlineMeetingController::class, 'update']);
        Route::delete('delete/{id}', [OnlineMeetingController::class, 'destroy']);
    });

    Route::prefix('other-activities')->group(function () {
        Route::get('index', [OtherActivitiesController::class, 'index']);
        Route::post('store', [OtherActivitiesController::class, 'store']);
        Route::get('show/{id}', [OtherActivitiesController::class, 'getSingleData']);
        Route::patch('update', [OtherActivitiesController::class, 'update']);
        Route::delete('delete/{id}', [OtherActivitiesController::class, 'destroy']);
    });

    Route::prefix('event')->group(function () {
        Route::get('index', [EventController::class, 'index']);
        Route::post('store', [EventController::class, 'store']);
        Route::get('show/{id}', [EventController::class, 'getSingleData']);
        Route::patch('update', [EventController::class, 'update']);
        Route::delete('delete/{id}', [EventController::class, 'destroy']);
        Route::get('report', [EventController::class, 'report']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('total-emissions', [DashboardController::class, 'totalEmissions']);
        Route::get('events-data', [DashboardController::class, 'eventsData']);
    });

    Route::prefix('content')->group(function () {
        Route::get('index', [ContentManageController::class, 'index']);
        Route::post('store', [ContentManageController::class, 'store']);
        Route::get('show/{id}', [ContentManageController::class, 'getSingleData']);
        Route::patch('update', [ContentManageController::class, 'update']);
        Route::delete('delete/{id}', [ContentManageController::class, 'destroy']);
    });
});
