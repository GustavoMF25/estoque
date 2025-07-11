<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\UsuarioController;
use App\Livewire\TesteLivewire;
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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['auth', 'perfil:admin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::resource('usuarios', UsuarioController::class);

        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
    });

    Route::resource('lojas', LojaController::class);
    Route::resource('estoques', EstoqueController::class);
    Route::patch('estoques/{id}/restaurar', [EstoqueController::class, 'restore'])->name('estoques.restore');

    Route::resource('produtos', ProdutosController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('produtos/catalogo', [ProdutosController::class , 'catalogo'])->name('produtos.catalogo');
    Route::get('/produtos/visualizar', [ProdutosController::class, 'show'])->name('produtos.show');
    Route::post('/produtos/vender', [ProdutosController::class, 'vender'])->name('produtos.vender');

    Route::resource('categorias', CategoriaController::class);
});
