<?php

use App\Http\Controllers\AssinaturasController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EmpresaModuloController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\NotaModeloController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\WebhookMercadoPagoController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Carrinho\ConfirmarVenda;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Features;
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

Route::get('/assinatura-expirada', function () {
    return view('assinaturas.expirada');
})->name('assinaturas.expirada');

if (Features::enabled(Features::registration())) {
    Route::middleware('guest')->group(function () {
        Route::get('/register', [RegisteredUserController::class, 'create'])
            ->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store']);
    });
}

Route::post('/api/mercadopago/webhook', [WebhookMercadoPagoController::class, 'handle']);
Route::get('faturas/{fatura}/pagamento/sucesso', [FaturaController::class, 'sucesso'])->name('faturas.pagamento.sucesso');
Route::get('faturas/{fatura}/pagamento/erro', [FaturaController::class, 'erro'])->name('faturas.pagamento.erro');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'assinatura.ativa'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware(['auth', 'perfil:superadmin,admin'])->group(function () {
        Route::get('assinaturas/minha', [AssinaturasController::class, 'minha'])->name('assinaturas.minha');
    });

    Route::middleware(['auth', 'perfil:superadmin'])->group(function () {
        // Route::resource('assinaturas', AssinaturasController::class);
        Route::post('assinaturas/{id}/renovar', [AssinaturasController::class, 'renovar'])->name('assinaturas.renovar');
        Route::get('assinaturas/verificar', [AssinaturasController::class, 'verificarVencidas'])->name('assinaturas.verificar');

        Route::get('assinaturas', [AssinaturasController::class, 'index'])->name('assinaturas.index');
        Route::get('assinaturas/create/{empresa}', [AssinaturasController::class, 'create'])->name('assinaturas.create');
        Route::post('assinaturas/store/{empresa}', [AssinaturasController::class, 'store'])->name('assinaturas.store');
        Route::get('assinaturas/{assinatura}', [AssinaturasController::class, 'show'])->name('assinaturas.show');
        Route::get('assinaturas/{assinatura}/editar', [AssinaturasController::class, 'edit'])->name('assinaturas.edit');
        Route::put('assinaturas/{assinatura}', [AssinaturasController::class, 'update'])->name('assinaturas.update');

        Route::get('assinaturas/{assinatura}/faturas', [FaturaController::class, 'index'])->name('faturas.index');
        Route::get('assinaturas/{assinatura}/faturas/create', [FaturaController::class, 'create'])->name('faturas.create');
        Route::post('assinaturas/{assinatura}/faturas', [FaturaController::class, 'store'])->name('faturas.store');
        Route::put('faturas/{fatura}/pagar', [FaturaController::class, 'marcarPago'])->name('faturas.marcarPago');
        Route::delete('faturas/{fatura}', [FaturaController::class, 'destroy'])->name('faturas.destroy');

        Route::get('{empresa}/modulos', [EmpresaModuloController::class, 'edit'])->name('empresas.modulos.edit');
        Route::put('{empresa}/modulos', [EmpresaModuloController::class, 'update'])->name('empresas.modulos.update');
    });

    Route::middleware(['auth', 'perfil:superadmin'])->group(function () {
        Route::resource('empresas', EmpresaController::class);
    });
    Route::middleware(['auth', 'perfil:admin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::resource('usuarios', UsuarioController::class);
        Route::get('/empresa', [EmpresaController::class, 'editEmpresa'])->name('empresa.editEmpresa');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::get('/auditoria', [AuditLogController::class, 'index'])->name('auditoria.index');

        Route::middleware(['modulo:estoques'])->group(function () {
            Route::resource('estoques', EstoqueController::class);
        });

        Route::middleware(['modulo:categorias'])->group(function () {
            Route::resource('categorias', CategoriaController::class);
        });
        Route::middleware(['modulo:fabricantes'])->group(function () {
            Route::resource('fabricantes', FabricanteController::class);
        });
    });

    Route::middleware(['modulo:lojas'])->group(function () {
        Route::resource('lojas', LojaController::class);
    });


    Route::patch('estoques/{id}/restaurar', [EstoqueController::class, 'restore'])->name('estoques.restore');

    Route::middleware(['modulo:produtos'])->group(function () {
        Route::resource('produtos', ProdutosController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::get('produtos/catalogo', [ProdutosController::class, 'catalogo'])->name('produtos.catalogo');
        Route::get('/produtos/visualizar', [ProdutosController::class, 'show'])->name('produtos.show');
    });

    Route::middleware(['modulo:vendas'])->group(function () {
        Route::post('/produtos/vender', [ProdutosController::class, 'vender'])->name('produtos.vender');
        Route::get('/carrinho/confirmar', ConfirmarVenda::class)->name('carrinho.confirmar');

        Route::get('/vendas', function () {
            return view('vendas.index');
        })->name('vendas.index');
        Route::get('/vendas/{venda}/nota', [VendaController::class, 'gerar'])
            ->name('vendas.nota');
    });


    Route::middleware(['modulo:clientes'])->group(function () {
        Route::resource('clientes', ClienteController::class);
    });
});
