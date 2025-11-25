<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// web.php
Route::get('/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/sync-tasks', [GoogleController::class, 'syncTasks']);