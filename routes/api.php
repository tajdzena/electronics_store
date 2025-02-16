<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products', [ProductController::class, 'index']); //Prikaz svih proizvoda
Route::get('/products/category/{category_id}', [ProductController::class, 'byCategoryId']); //Proizvodi po kategoriji, preko id-a
Route::get('/products/category', [ProductController::class, 'byCategoryName']);
//Proizvodi po kategoriji, primer preko imena kao parametar, isto to moze da se implementira i za kategorije, i za brisanje
//npr. localhost:8000/api/products/category?name=ime sa razmacima (radi i sa zagradama)
//Izuzetak je ampersand - mora se uneti kao %26 direkt u url, jer ga u suprotnom prepoznaje za razdvajanje parametara
Route::delete('/products/{upc}', [ProductController::class, 'destroy']); //Brisanje proizvoda
Route::put('/products/{upc}', [ProductController::class, 'update']); //Izmena proizvoda

//Bonus zadatak, .csv fajl proizvoda po kategoriji
Route::get('/products/csv/{category_id}', [ProductController::class, 'generateCSV']);

Route::get('/categories', [CategoryController::class, 'index']); //Prikaz svih kategorija
Route::delete('/categories/{category_id}', [CategoryController::class, 'destroy']); //Brisanje kategorije
Route::put('/categories/{category_id}', [CategoryController::class, 'update']); //Izmena imena kategorije

