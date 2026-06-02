<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('auth.login');
});
Route::get('/register', function () {
    return view('auth.register');
});





Route::get('/home', function () {
    return view('page_user.home');
});
Route::get('/product', function () {
    return view('page_user.product');
});
Route::get('/bestseller', function () {
    return view('page_user.bestseller');
});
Route::get('/sale', function () {
    return view('page_user.sale');
});
Route::get('/profileuser', function () {
    return view('page_user.profileuser');
});
Route::get('/detail', function () {
    return view('page_user.detail');
});
Route::get('/cart', function () {
    return view('page_user.cart');
});
Route::get('/checkout', function () {
    return view('page_user.checkout');
});