<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PaymentController;

Route::get('styleguide', function() {
    return view('styleguide');
});

Route::get('/login', [AuthController::class, 'getLogin'])->name('getLogin');
Route::post('/login', [AuthController::class, 'postLogin'])->name('postLogin');
Route::get('/register', [AuthController::class, 'getRegister'])->name('getRegister');
Route::post('/register', [AuthController::class, 'postRegister'])->name('postRegister');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/logout', [AuthController::class, 'getLogout'])->name('getLogout');
    Route::post('/logout', [AuthController::class, 'postLogout'])->name('postLogout');

    // User Messages
    Route::prefix('/messages/{message_id?}')->group(function () {
        Route::get('/', [UserController::class, 'getMessages'])->name('getMessages');
        Route::post('/delete', [UserController::class, 'postDeleteMessage'])->name('postDeleteMessage');
    });

    // User Profile
    Route::prefix('/profile')->group(function () {
        Route::get('/', [UserController::class, 'getProfile'])->name('getProfile');
        Route::post('/update', [UserController::class, 'postUpdateProfile'])->name('postUpdateProfile');
    });
    
    // Instance Invite
    Route::get('/accept/{invite_token}', [UserController::class, 'getAcceptInvite'])->name('getAcceptInvite');

    Route::post('/create', [InstanceController::class, 'postCreateInstance'])->name('postCreateInstance');
    Route::middleware('instanceAccess')->prefix('{instance_id?}')->group(function () {
        Route::get('/', [InstanceController::class, 'getInstanceContext'])->name('getInstanceContext');
        Route::post('/delete', [InstanceController::class, 'postDeleteInstance'])->name('postDeleteInstance');

        // Instance Settings
        Route::prefix('settings')->group(function () {
            Route::get('/', [InstanceController::class, 'getInstanceSettings'])->name('getInstanceSettings');
            Route::post('/update', [InstanceController::class, 'postInstanceUpdate'])->name('postInstanceUpdate');
            Route::post('/renew-api-key', [InstanceController::class, 'postRenewApiKey'])->name('postRenewApiKey');
        });

        // Instance Roles & Permissions
        Route::prefix('roles')->group(function () {
            Route::get('/{role_id?}', [InstanceController::class, 'getInstancePermissions'])->name('getInstancePermissions');
            Route::post('/create', [InstanceController::class, 'postCreateRole'])->name('postCreateRole');
            Route::post('/delete/{role_id}', [InstanceController::class, 'postDeleteRole'])->name('postDeleteRole');
            Route::post('/update/{role_id}', [InstanceController::class, 'postUpdateRole'])->name('postUpdateRole');
        });

        // Instance Users
        Route::prefix('users')->group(function () {
            Route::get('/invite', [InstanceController::class, 'getInstanceInvite'])->name('getInstanceInvite');
            Route::post('/invite', [InstanceController::class, 'postInviteUser'])->name('postInviteUser');
            Route::get('/{user_id?}', [InstanceController::class, 'getInstanceUsers'])->name('getInstanceUsers');
            Route::post('/update/{user_id}', [InstanceController::class, 'postUpdateUser'])->name('postUpdateUser');
            Route::post('/kick/{user_id}', [InstanceController::class, 'postKickUser'])->name('postKickUser');
        });

        // Instance Modules
        Route::prefix('modules')->group(function() {
            Route::get('/{instance_module_id?}', [InstanceController::class, 'getInstanceModules'])->name('getInstanceModules');
            Route::post('/save', [InstanceController::class, 'postSaveModules'])->name('postSaveModules');
            // Module Payment
            Route::prefix('payment')->group(function() {
                Route::get('/{instance_module_id}', [PaymentController::class, 'getModuleCheckout'])->name('getModuleCheckout');
                Route::post('/{instance_module_id}', [PaymentController::class, 'postModuleCheckout'])->name('postModuleCheckout');
            });
        });

    });

});

// API Routes
Route::match(["get","post","put","patch","delete"], '/api/{api_access_key}', [ApiController::class, 'matchApiCall'])->name('api');

// Payment Webhook
Route::post('/payment/webhook', [PaymentController::class, 'postWebhook'])->name('postWebhook');

// Cron Routes
Route::get('/cron/{cron_access_key}', [CronController::class, 'cron'])->name('cron');

// Frontend Routes
Route::prefix('/{instance_module_id?}')->group(function () {
    Route::get('/', [FrontendController::class, 'getFrontendView'])->name('getFrontendView');

    // ========================================== FRONTEND MODULE ROUTES ==========================================
    // Test Module
    Route::prefix('testmodule')->group(function () {
        Route::post('/send-message', [\App\Http\ModuleControllers\TestController::class, 'postSendMessage'])->name('TestModule_postSendMessage');
    });
});