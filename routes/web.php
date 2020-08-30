<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('/upload_csv');
});


Route::view('/upload_csv', 'upload');
Route::post('/upload_csv', 'Upload@upload_file');
Route::post('/add_column', 'Upload@add_column');
