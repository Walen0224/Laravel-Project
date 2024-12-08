<?php

use App\Http\Controllers\User\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->name('user.')->middleware(['auth'])->group(function () {
    // 我的商品頁面
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');
    // 商品上下架
    Route::put('/products/{product}/demote', [ProductController::class, 'demoteData'])
        ->name('products.demoteData');
    // 商品修改頁面
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');
    // 商品>修改 資料
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');

    // 商品刪除
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');

    // 刊登商品頁面
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');
    // 商品建立資料
    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

});
