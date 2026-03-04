<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\FuncionalidadeController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\NotaModeloController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PerfilFuncionalidadeController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\ProdutoChegadaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendaController;
use App\Livewire\Carrinho\ConfirmarVenda;
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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'funcionalidade'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['auth', 'perfil:admin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::resource('usuarios', UsuarioController::class);

        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::get('/auditoria', [AuditLogController::class, 'index'])->name('auditoria.index');
        Route::resource('nota-modelos', NotaModeloController::class);
        Route::resource('perfis', PerfilController::class)
            ->parameters(['perfis' => 'perfil'])
            ->except(['show']);
        Route::resource('funcionalidades', FuncionalidadeController::class)->except(['show']);
        Route::get('/perfil-funcionalidades', [PerfilFuncionalidadeController::class, 'edit'])->name('perfil-funcionalidades.edit');
    });

    Route::resource('lojas', LojaController::class)->except(['show']);
    Route::resource('estoques', EstoqueController::class);
    Route::patch('estoques/{id}/restaurar', [EstoqueController::class, 'restore'])->name('estoques.restore');

    Route::resource('produtos', ProdutosController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('produtos/catalogo', [ProdutosController::class, 'catalogo'])->name('produtos.catalogo');
    Route::get('estoque-a-chegar', [ProdutoChegadaController::class, 'index'])->name('produto-chegadas.index');
    Route::middleware('perfil:admin,operador')->group(function () {
        Route::post('estoque-a-chegar', [ProdutoChegadaController::class, 'store'])->name('produto-chegadas.store');
        Route::get('estoque-a-chegar/{produtoChegada}/editar', [ProdutoChegadaController::class, 'edit'])->name('produto-chegadas.edit');
        Route::put('estoque-a-chegar/{produtoChegada}', [ProdutoChegadaController::class, 'update'])->name('produto-chegadas.update');
        Route::post('estoque-a-chegar/{produtoChegada}/receber', [ProdutoChegadaController::class, 'receber'])->name('produto-chegadas.receber');
        Route::post('estoque-a-chegar/{produtoChegada}/cancelar', [ProdutoChegadaController::class, 'cancelar'])->name('produto-chegadas.cancelar');
    });
    Route::get('/produtos/visualizar', [ProdutosController::class, 'show'])->name('produtos.show');
    Route::post('/produtos/vender', [ProdutosController::class, 'vender'])->name('produtos.vender');
    Route::patch('/produtos/{produto}/desativar', [ProdutosController::class, 'desativar'])
        ->middleware('perfil:admin')
        ->name('produtos.desativar');

    Route::resource('categorias', CategoriaController::class);
    Route::resource('fabricantes', FabricanteController::class);
    Route::resource('clientes', ClienteController::class);
    Route::get('/notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
    Route::patch('/notificacoes/{notificacao}/ler', [NotificacaoController::class, 'marcarComoLida'])
        ->name('notificacoes.ler');

    Route::middleware(['block.old.sales'])->group(function () {
        Route::get('/carrinho/confirmar', ConfirmarVenda::class)->name('carrinho.confirmar');

        Route::get('/vendas', function () {
            return view('vendas.index');
        })->name('vendas.index');
        Route::get('/vendas/{venda}/nota', [VendaController::class, 'gerar'])
            ->name('vendas.nota');
        Route::get('/vendas/nota/editavel/{emissao}', [VendaController::class, 'gerarEditavel'])
            ->name('vendas.nota.editavel');
    });
});
