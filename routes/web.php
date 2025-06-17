<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CharacterController;

Route::get('/', [CharacterController::class, 'index'])->name('characters.index');
Route::get('/characters/fetch', [CharacterController::class, 'fetchCharacters'])->name('characters.fetch');
Route::post('/characters/store', [CharacterController::class, 'store'])->name('characters.store');
Route::get('/characters/stored', [CharacterController::class, 'getStoredCharacters'])->name('characters.stored');
Route::put('/characters/{character}', [CharacterController::class, 'update'])->name('characters.update');
