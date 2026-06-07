<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('auth.login');
});
Route::get('/register', function () {
    return view('auth.register');
});




// User Routes
Route::prefix('user')->group(function () {
    
    Route::get('/home', [ProductController::class, 'home']);

    Route::get('/product', [ProductController::class, 'index']);

    Route::get('/bestseller', [ProductController::class, 'bestseller']);
    Route::get('/sale', [ProductController::class, 'sale']);

    Route::get('/profileuser', function () {
        return view('page_user.profileuser');
    });

    Route::get('/detail/{id}', [ProductController::class, 'detail']);

    Route::get('/cart', function () {
        return view('page_user.cart');
    });

    Route::get('/checkout', function () {
        return view('page_user.checkout');
    });

});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/products', function () {
        return view('admin.products');
    })->name('admin.products');

    Route::get('/categories', function () {
        return view('admin.categories');
    })->name('admin.categories');

    Route::get('/orders', function () {
        return view('admin.orders');
    })->name('admin.orders');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('admin.users');

    Route::get('/statistics', function () {
        return view('admin.statistics');
    })->name('admin.statistics');
});
