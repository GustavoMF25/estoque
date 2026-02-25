<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\InstalacaoController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\ProdutoChegadaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/instalacao', [InstalacaoController::class, 'index'])->name('instalacao.index');
Route::post('/instalacao/banco', [InstalacaoController::class, 'salvarBanco'])->name('instalacao.banco');
Route::post('/instalacao/setup', [InstalacaoController::class, 'executarSetup'])->name('instalacao.setup');
Route::post('/instalacao/cadastro-inicial', [InstalacaoController::class, 'salvarCadastroInicial'])->name('instalacao.cadastro-inicial');
Route::post('/instalacao/ativar', [InstalacaoController::class, 'ativar'])->name('instalacao.ativar');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['auth', 'perfil:admin'])->group(function () {
        Route::resource('usuarios', UsuarioController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::get('/auditoria', [AuditLogController::class, 'index'])->name('auditoria.index');
    });

    Route::resource('lojas', LojaController::class)->except(['show']);
    Route::resource('estoques', EstoqueController::class)->except(['show']);
    Route::patch('estoques/{id}/restaurar', [EstoqueController::class, 'restore'])->name('estoques.restore');

    Route::resource('produtos', ProdutosController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('estoque-a-chegar', [ProdutoChegadaController::class, 'index'])->name('produto-chegadas.index');

    Route::middleware('perfil:admin,operador')->group(function () {
        Route::post('estoque-a-chegar', [ProdutoChegadaController::class, 'store'])->name('produto-chegadas.store');
        Route::post('estoque-a-chegar/{produtoChegada}/receber', [ProdutoChegadaController::class, 'receber'])->name('produto-chegadas.receber');
        Route::post('estoque-a-chegar/{produtoChegada}/cancelar', [ProdutoChegadaController::class, 'cancelar'])->name('produto-chegadas.cancelar');
    });

    Route::get('/produtos/visualizar', [ProdutosController::class, 'show'])->name('produtos.show');
    Route::patch('/produtos/{produto}/desativar', [ProdutosController::class, 'desativar'])
        ->middleware('perfil:admin')
        ->name('produtos.desativar');

    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('fabricantes', FabricanteController::class)->only(['index', 'create', 'store', 'destroy']);
});
