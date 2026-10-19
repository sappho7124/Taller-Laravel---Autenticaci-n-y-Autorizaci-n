<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('eventos.index');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login.view');

Route::get('/eventos', function () {
    return view('eventos.index');
})->name('eventos.index');

Route::get('/metricas', function () {
    return view('admin.metricas');
})->name('admin.metricas');