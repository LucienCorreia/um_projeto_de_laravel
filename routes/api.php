<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\LivrosController;

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

Route::prefix('v1')
    ->middleware(['api'])->group(function ($router) {
        Route::post('auth/token', [AutenticacaoController::class, 'token']);

        Route::prefix('livros')
        ->middleware(['auth:api'])
            ->group(function ($router) {
                Route::get('/', [LivrosController::class, 'index']);
                Route::post('/{livro}/importar-indices-xml', [LivrosController::class, 'showXml']);
                Route::post('/', [LivrosController::class, 'store']);
            });
    });
