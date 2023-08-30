<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('movie.index');
});

Route::any('/scrape', [ScrapeController::class,'scrape'])->name('scrape');
Route::get('/movie', [ScrapeController::class,'movie'])->name('movie');
Route::get('/movie-list', [ScrapeController::class,'movieList'])->name('movie-list');
