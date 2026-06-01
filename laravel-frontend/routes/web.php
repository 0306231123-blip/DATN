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