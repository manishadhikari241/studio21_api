<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\CMS\UsersController;
use App\Http\Controllers\API\CMS\ConfigurationController;
use App\Http\Controllers\API\CMS\TimeSlotController;
use App\Http\Controllers\API\CMS\SlideShowsController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\StripePaymentController;
use App\Http\Controllers\API\AddressBookController;
use \App\Http\Controllers\API\AppController;
use \App\Http\Controllers\API\CMS\CouponsController;
use \App\Http\Controllers\API\CMS\ClosureController;
use \App\Http\Controllers\API\CMS\PagesController;

//middleware throttle
//->middleware('throttle:2,100');
//->middleware('throttle:3,1'); gives 3 request every 1 minute

Route::group(['middleware' => 'api'], function ($router) {

    Route::get('init', [AppController::class, 'init']);
    Route::get('/pages/{slug}', [PagesController::class, 'show']);
    Route::get('/reservation/availableSlots', [ReservationController::class, 'getAvailableSlots']);
    Route::get('/reservation/getActiveDateSlot', [ReservationController::class, 'getActiveDateSlot']);

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/verify', [AuthController::class, 'verify']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/userProfile', [AuthController::class, 'userProfile']);
        Route::put('/updateProfile', [AuthController::class, 'updateProfile']);
        Route::put('/changePassword', [AuthController::class, 'changePassword']);
        Route::post('/resendCode', [AuthController::class, 'resendCode'])->middleware('throttle:4,120');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('/login-as', [AuthController::class, 'loginAs'])->middleware('superAdmin');
    });
    Route::group(['middleware' => 'auth:api',], function () {
        Route::get('/reservation/getById', [ReservationController::class, 'getById']);
        Route::delete('/reservation/cancelBookings', [ReservationController::class, 'cancelBooking']);
        Route::apiResource('reservation', ReservationController::class);
        Route::apiResource('addressBook', AddressBookController::class);
        Route::post('stripe', [StripePaymentController::class, 'stripePost'])->name('stripePay');
        Route::get('/coupons', [\App\Http\Controllers\API\CouponsController::class, 'check']);
        Route::get('/coupons/repPaymentsByUser', [\App\Http\Controllers\API\CouponsController::class, 'repPaymentsByUser'])->middleware('representative');
        Route::post('/coupons/inviteCoupon', [\App\Http\Controllers\API\CouponsController::class, 'inviteCoupon'])->middleware('representative');
    });
    Route::group(['prefix' => 'cms'], function () {
        Route::group(['middleware' => 'superAdmin'], function () {
            Route::match(['get', 'post'], '/configuration', [ConfigurationController::class, 'index']);
            Route::get('/reservation/calendar', [\App\Http\Controllers\API\CMS\ReservationController::class, 'getCalendarData']);
            Route::get('/reservation/', [\App\Http\Controllers\API\CMS\ReservationController::class, 'index']);
            Route::post('/coupons/attachRepresentative', [CouponsController::class, 'attachRepresentative']);
            Route::put('/coupons/payRepresentative', [CouponsController::class, 'payRepresentative']);
            Route::get('/coupons/allRepPayments', [CouponsController::class, 'allRepPayments']);
            Route::post('/closure/customClosure', [ClosureController::class, 'customClosure']);
            Route::delete('/closure/customClosure', [ClosureController::class, 'resetCustomClosure']);
            Route::post('/closure/weeklyClosure', [ClosureController::class, 'weeklyClosure']);
            Route::delete('/closure/weeklyClosure', [ClosureController::class, 'resetWeeklyClosure']);
            Route::get('/timeSlots/getActiveSlots', [TimeSlotController::class, 'getActiveSlots']);
            Route::get('/pages', [PagesController::class, 'all']);
            Route::put('/pages', [PagesController::class, 'updatePages']);
            Route::apiResources([
                'closure' => ClosureController::class,
                'users' => UsersController::class,
                'timeSlots' => TimeSlotController::class,
                'slideShows' => SlideShowsController::class,
                'coupons' => CouponsController::class
            ]);

        });
    });
});

