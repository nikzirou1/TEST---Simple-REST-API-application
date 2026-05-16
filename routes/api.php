<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Book Library — API Routes
|--------------------------------------------------------------------------
| All routes are automatically prefixed with /api by Laravel.
|
| Resource routes registered:
|   GET    /api/books          → BookController@index
|   POST   /api/books          → BookController@store
|   GET    /api/books/{book}   → BookController@show
|   PATCH  /api/books/{book}   → BookController@update
|   DELETE /api/books/{book}   → BookController@destroy
*/

Route::apiResource('books', BookController::class)->only([
    'index', 'store', 'show', 'update', 'destroy',
]);
