<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Categories;


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

Route::get('/', [Categories::class, 'index']);
Route::get('/ajax/getsubcategories/{id}/{level}', [Categories::class, 'getsubcategories']);
Route::post('/ajax/addsubcategory', [Categories::class, 'addsubcategory']);
Route::post('/ajax/removesubcategory', [Categories::class, 'removesubcategory']);


