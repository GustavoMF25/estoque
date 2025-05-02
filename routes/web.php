<?php

use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::resource('usuarios', UsuarioController::class);


    Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

    Route::resource('lojas', LojaController::class);
    Route::resource('estoques', EstoqueController::class);
});
