<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseDetailController;
use App\Http\Controllers\Stock_in;
use App\Http\Controllers\StockInDetailController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\StockOutDetailController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return apiResponse($request->user()->fresh(), 200, 'get authenticated user successfully');
    });
    Route::post('/me', [UserController::class, 'updateProfile']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::middleware('admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('/get-users', [UserController::class, 'getUser']);
        Route::get('/get-users/{id}', [UserController::class, 'getUserById']);
        Route::get('/user-details', [UserController::class, 'userDetails']);
        Route::get('/user-details/{id}', [UserController::class, 'userDetailById']);
    });

    // Read routes are shared by every active inventory role. The staff
    // middleware accepts admin, manager, and staff users.
    Route::middleware('staff')->group(function () {
        Route::get('/products', [ProductController::class, 'display']);
        Route::get('/products/{id}', [ProductController::class, 'display']);
        Route::get('/categories', [CategoryController::class, 'display']);
        Route::get('/categories/{id}', [CategoryController::class, 'display']);
        Route::get('/suppliers', [SupplierController::class, 'display']);
        Route::get('/suppliers/{id}', [SupplierController::class, 'display']);
        Route::get('/purchases', [PurchaseController::class, 'display']);
        Route::get('/purchases/{id}', [PurchaseController::class, 'display']);
        Route::get('/purchase-details', [PurchaseDetailController::class, 'index']);
        Route::get('/purchase-details/{purchase_detail}', [PurchaseDetailController::class, 'show']);
        Route::get('/stock-ins', [Stock_in::class, 'display']);
        Route::get('/stock-ins/{id}', [Stock_in::class, 'display']);
        Route::get('/stock-in-details', [StockInDetailController::class, 'index']);
        Route::get('/stock-in-details/{stock_in_detail}', [StockInDetailController::class, 'show']);
        Route::get('/stock-outs', [StockOutController::class, 'display']);
        Route::get('/stock-outs/{id}', [StockOutController::class, 'display']);
        Route::get('/stock-out-details', [StockOutDetailController::class, 'index']);
        Route::get('/stock-out-details/{stock_out_detail}', [StockOutDetailController::class, 'show']);

        Route::post('/stock-ins', [Stock_in::class, 'create']);
        Route::post('/stock-in-details', [StockInDetailController::class, 'store']);
        Route::post('/stock-outs', [StockOutController::class, 'create']);
        Route::post('/stock-out-details', [StockOutDetailController::class, 'store']);
    });

    Route::middleware('manager')->group(function () {
        Route::post('/categories', [CategoryController::class, 'create']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::patch('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'delete']);

        Route::post('/suppliers', [SupplierController::class, 'create']);
        Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
        Route::patch('/suppliers/{id}', [SupplierController::class, 'update']);
        Route::delete('/suppliers/{id}', [SupplierController::class, 'delete']);

        Route::post('/purchases', [PurchaseController::class, 'create']);
        Route::put('/purchases/{id}', [PurchaseController::class, 'update']);
        Route::patch('/purchases/{id}', [PurchaseController::class, 'update']);
        Route::delete('/purchases/{id}', [PurchaseController::class, 'delete']);

        Route::post('/purchase-details', [PurchaseDetailController::class, 'store']);
        Route::put('/purchase-details/{purchase_detail}', [PurchaseDetailController::class, 'update']);
        Route::patch('/purchase-details/{purchase_detail}', [PurchaseDetailController::class, 'update']);
        Route::delete('/purchase-details/{purchase_detail}', [PurchaseDetailController::class, 'destroy']);

        Route::post('/products', [ProductController::class, 'create']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::patch('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'delete']);

        Route::put('/stock-ins/{id}', [Stock_in::class, 'update']);
        Route::patch('/stock-ins/{id}', [Stock_in::class, 'update']);
        Route::delete('/stock-ins/{id}', [Stock_in::class, 'delete']);

        Route::put('/stock-in-details/{stock_in_detail}', [StockInDetailController::class, 'update']);
        Route::patch('/stock-in-details/{stock_in_detail}', [StockInDetailController::class, 'update']);
        Route::delete('/stock-in-details/{stock_in_detail}', [StockInDetailController::class, 'destroy']);

        Route::put('/stock-outs/{id}', [StockOutController::class, 'update']);
        Route::patch('/stock-outs/{id}', [StockOutController::class, 'update']);
        Route::delete('/stock-outs/{id}', [StockOutController::class, 'delete']);

        Route::put('/stock-out-details/{stock_out_detail}', [StockOutDetailController::class, 'update']);
        Route::patch('/stock-out-details/{stock_out_detail}', [StockOutDetailController::class, 'update']);
        Route::delete('/stock-out-details/{stock_out_detail}', [StockOutDetailController::class, 'destroy']);
    });
});
