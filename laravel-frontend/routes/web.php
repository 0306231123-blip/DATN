<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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
    
    Route::get('/home', function () {
        return view('page_user.home');
    });

    Route::get('/product', [ProductController::class, 'index']);

    Route::get('/bestseller', function () {
        return view('page_user.bestseller');
    });

    Route::get('/sale', function () {
        return view('page_user.sale');
    });

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
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

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

