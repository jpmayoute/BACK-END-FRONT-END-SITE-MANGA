<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\CategoryController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return 'Bienvenu sur ma future API de Manga';
});

//route pour récuperer la globalité des mangas
// Route de récupération des mangas
// Type : get
// Chemin : http://127.0.0.1:8000/api/mangas
// Controller : MangaController
// Méthode : index
Route::get('/mangas', [MangaController::class, 'index']);

//route pour récupérer un manga précis
Route::get('/mangas/{id}', [MangaController::class, 'show'])->where('id', '[0-9]+');

//route pour ajouter un manga
Route::post('/mangas', [MangaController::class, 'create']);

//route pour modifier tout un manga
Route::put('/mangas/{id}', [MangaController::class, 'update'])->where('id', '[0-9]+');

//route pour supprimer un manga
Route::delete('/mangas/{id}', [MangaController::class, 'delete'])->where('id', '[0-9]+');

//route pour modifier un seul champ d'un manga
Route::patch('/mangas/{id}', [MangaController::class, "update"])->where('id', '[0-9]+');

Route::get('/categories', [CategoryController::class, "list"]);
