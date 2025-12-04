<?php

namespace App\Http\Controllers;

use App\Models\Assinaturas;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Estoque;
use App\Models\Fatura;
use App\Models\Movimentacao;
use App\Models\Produto;
use App\Models\ProdutosUnidades;
use App\Models\User;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();

        return view('dashboard', [
            'perfil' => $user?->perfil ?? 'operador',
            'cards' => $this->cardsFor($user),
            'lists' => array_values(array_filter($this->listsFor($user))),
        ]);
    }

    protected function cardsFor(?User $user): array
    {
        if ($user?->isSuperAdmin()) {
            return $this->cardsSuperAdmin();
        }

        if ($user?->isAdmin()) {
            return $this->cardsAdmin();
        }

        return $this->cardsOperacional($user);
    }

    protected function cardsSuperAdmin(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $faturamentoMes = Fatura::where('status', 'pago')
            ->whereBetween('updated_at', [$inicioMes, $fimMes])
            ->sum('valor');

        $renovacoesUrgentes = Assinaturas::whereBetween('data_vencimento', [now(), now()->addDays(3)])
            ->whereNotIn('status', ['cancelado'])
            ->count();

        $empresasComModulosInativos = Empresa::whereHas('modulos', function ($q) {
            $q->where(function ($sub) {
                $sub->where('empresa_modulos.ativo', false)
                    ->orWhere('empresa_modulos.status', '!=', 'ativo');
            });
        })->count();

        return [
            [
                'title' => 'Empresas ativas',
                'value' => Empresa::count(),
                'subtitle' => 'Organizações acompanhadas pela plataforma',
                'icon' => 'building',
                'variant' => 'primary',
            ],
            [
                'title' => 'Assinaturas em dia',
                'value' => Assinaturas::where('status', 'ativo')->count(),
                'subtitle' => 'Clientes com assinatura ativa',
                'icon' => 'shield-alt',
                'variant' => 'success',
            ],
            [
                'title' => 'Assinaturas em atenção',
                'value' => Assinaturas::whereIn('status', ['pendente', 'atrasado'])->count(),
                'subtitle' => 'Pendentes ou atrasadas',
                'icon' => 'exclamation-triangle',
                'variant' => 'warning',
            ],
            [
                'title' => 'Faturamento no mês',
                'value' => $faturamentoMes,
                'subtitle' => 'Somatório das faturas pagas',
                'icon' => 'coins',
                'variant' => 'info',
                'is_money' => true,
            ],
            [
                'title' => 'Renovações urgentes',
                'value' => $renovacoesUrgentes,
                'subtitle' => 'Vencem em até 3 dias',
                'icon' => 'calendar-exclamation',
                'variant' => 'danger',
            ],
            [
                'title' => 'Empresas com módulos inativos',
                'value' => $empresasComModulosInativos,
                'subtitle' => 'Precisam de revisão de permissões',
                'icon' => 'tools',
                'variant' => 'warning',
            ],
        ];
    }

    protected function cardsAdmin(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $valorVendidoMes = Venda::whereBetween('created_at', [$inicioMes, $fimMes])
            ->sum(DB::raw('COALESCE(valor_final, valor_total, 0)'));

        return [
            [
                'title' => 'Produtos ativos',
                'value' => Produto::count(),
                'subtitle' => 'Itens cadastrados e disponíveis',
                'icon' => 'boxes',
                'variant' => 'info',
            ],
            [
                'title' => 'Unidades disponíveis',
                'value' => ProdutosUnidades::where('status', 'disponivel')->count(),
                'subtitle' => 'Prontas para venda',
                'icon' => 'layer-group',
                'variant' => 'success',
            ],
            [
                'title' => 'Vendas no mês',
                'value' => $valorVendidoMes,
                'subtitle' => 'Receita consolidada no período',
                'icon' => 'chart-line',
                'variant' => 'primary',
                'is_money' => true,
            ],
            [
                'title' => 'Clientes ativos',
                'value' => Cliente::ativos()->count(),
                'subtitle' => 'Clientes aptos a comprar',
                'icon' => 'user-friends',
                'variant' => 'secondary',
            ],
        ];
    }

    protected function cardsOperacional(?User $user): array
    {
        $baseQuery = Venda::whereDate('created_at', Carbon::today());

        if ($user?->isVendedor()) {
            $baseQuery->where('user_id', $user->id);
        }

        $vendasHojeQuery = clone $baseQuery;

        return [
            [
                'title' => 'Vendas hoje',
                'value' => (clone $vendasHojeQuery)->count(),
                'subtitle' => 'Pedidos registrados no dia',
                'icon' => 'shopping-cart',
                'variant' => 'primary',
            ],
            [
                'title' => 'Receita do dia',
                'value' => $baseQuery->sum(DB::raw('COALESCE(valor_final, valor_total, 0)')),
                'subtitle' => $user?->isVendedor()
                    ? 'Somente suas vendas confirmadas'
                    : 'Total consolidado do time',
                'icon' => 'hand-holding-usd',
                'variant' => 'success',
                'is_money' => true,
            ],
            [
                'title' => 'Clientes atendidos',
                'value' => (clone $vendasHojeQuery)->whereNotNull('cliente_id')->distinct('cliente_id')->count('cliente_id'),
                'subtitle' => 'Clientes únicos com venda hoje',
                'icon' => 'users',
                'variant' => 'info',
            ],
            [
                'title' => 'Unidades disponíveis',
                'value' => ProdutosUnidades::where('status', 'disponivel')->count(),
                'subtitle' => 'Itens prontos para venda',
                'icon' => 'warehouse',
                'variant' => 'secondary',
            ],
        ];
    }

    protected function listsFor(?User $user): array
    {
        if ($user?->isSuperAdmin()) {
            return [
                $this->assinaturasProximasList(),
                $this->empresasComModulosDesativadosList(),
                $this->empresasRecentesList(),
            ];
        }

        if ($user?->isAdmin()) {
            return [
                $this->ultimasMovimentacoesList(),
                $this->vendasRecentesList(),
            ];
        }

        return [
            $this->produtosComEstoqueList(),
        ];
    }

    protected function assinaturasProximasList(): ?array
    {
        $assinaturas = Assinaturas::with('empresa')
            ->whereBetween('data_vencimento', [Carbon::now(), Carbon::now()->addDays(15)])
            ->orderBy('data_vencimento')
            ->limit(5)
            ->get();

        if ($assinaturas->isEmpty()) {
            return null;
        }

        return [
            'title' => 'Assinaturas próximas do vencimento',
            'variant' => 'warning',
            'items' => $assinaturas->map(function (Assinaturas $assinatura) {
                return [
                    'primary' => $assinatura->empresa->nome ?? 'Empresa #' . $assinatura->empresa_id,
                    'secondary' => 'Vence em ' . optional($assinatura->data_vencimento)->format('d/m/Y'),
                    'badge' => strtoupper($assinatura->status),
                    'badge_variant' => $assinatura->status === 'ativo' ? 'success' : 'warning',
                    'action_route' => route('assinaturas.show', $assinatura),
                    'action_label' => 'Gerenciar',
                ];
            })->all(),
        ];
    }

    protected function empresasComModulosDesativadosList(): ?array
    {
        $empresas = Empresa::with(['modulos' => function ($q) {
            $q->where(function ($sub) {
                $sub->where('empresa_modulos.ativo', false)
                    ->orWhere('empresa_modulos.status', '!=', 'ativo');
            });
        }])->whereHas('modulos', function ($q) {
            $q->where(function ($sub) {
                $sub->where('empresa_modulos.ativo', false)
                    ->orWhere('empresa_modulos.status', '!=', 'ativo');
            });
        })->limit(5)->get();

        if ($empresas->isEmpty()) {
            return null;
        }

        return [
            'title' => 'Empresas com módulos desativados',
            'variant' => 'danger',
            'items' => $empresas->map(function (Empresa $empresa) {
                $modulos = $empresa->modulos->pluck('nome')->join(', ');

                return [
                    'primary' => $empresa->nome,
                    'secondary' => 'Módulos: ' . ($modulos ?: 'Nenhum informado'),
                    'badge' => 'Ajustar',
                    'badge_variant' => 'danger',
                    'action_route' => route('empresas.modulos.edit', $empresa),
                    'action_label' => 'Configurar',
                ];
            })->all(),
        ];
    }

    protected function empresasRecentesList(): ?array
    {
        $empresas = Empresa::latest()->limit(5)->get();

        if ($empresas->isEmpty()) {
            return null;
        }

        return [
            'title' => 'Últimas empresas cadastradas',
            'variant' => 'info',
            'items' => $empresas->map(function (Empresa $empresa) {
                return [
                    'primary' => $empresa->nome,
                    'secondary' => 'Desde ' . optional($empresa->created_at)->format('d/m/Y'),
                    'badge' => $empresa->cnpj ? 'CNPJ ' . $empresa->cnpj : null,
                    'badge_variant' => 'primary',
                ];
            })->all(),
        ];
    }

    protected function ultimasMovimentacoesList(): ?array
    {
        $movimentacoes = Movimentacao::with('produto')
            ->latest()
            ->limit(6)
            ->get();

        if ($movimentacoes->isEmpty()) {
            return null;
        }

        return [
            'title' => 'Últimas movimentações de estoque',
            'variant' => 'secondary',
            'items' => $movimentacoes->map(function (Movimentacao $movimentacao) {
                return [
                    'primary' => $movimentacao->produto->nome ?? 'Produto #' . $movimentacao->produto_id,
                    'secondary' => ucfirst($movimentacao->tipo) . ' • ' . optional($movimentacao->created_at)->diffForHumans(),
                    'badge' => 'x' . $movimentacao->quantidade,
                    'badge_variant' => in_array($movimentacao->tipo, ['entrada', 'disponivel']) ? 'success' : 'danger',
                ];
            })->all(),
        ];
    }

    protected function vendasRecentesList(): ?array
    {
        $vendas = Venda::with(['cliente', 'usuario'])
            ->latest()
            ->limit(5)
            ->get();

        if ($vendas->isEmpty()) {
            return null;
        }

        return [
            'title' => 'Vendas recentes',
            'variant' => 'success',
            'items' => $vendas->map(function (Venda $venda) {
                $valor = (float) ($venda->valor_final ?? $venda->valor_total ?? 0);

                return [
                    'primary' => $venda->cliente->nome ?? 'Venda #' . $venda->id,
                    'secondary' => 'Por ' . ($venda->usuario->name ?? 'N/A') . ' • ' . optional($venda->created_at)->format('d/m H:i'),
                    'badge' => 'R$ ' . number_format($valor, 2, ',', '.'),
                    'badge_variant' => 'success',
                ];
            })->all(),
        ];
    }

    protected function produtosComEstoqueList(): ?array
    {
        $produtos = Produto::with(['estoque'])
            ->withCount([
                'unidades as disponiveis_count' => function ($q) {
                    $q->where('status', 'disponivel');
                },
            ])
            ->orderByDesc('disponiveis_count')
            ->limit(5)
            ->get();

        if ($produtos->isEmpty()) {
            return null;
        }

        return [
            'title' => 'Produtos com maior estoque',
            'variant' => 'info',
            'items' => $produtos->map(function (Produto $produto) {
                return [
                    'primary' => $produto->nome,
                    'secondary' => 'Disponíveis: ' . $produto->disponiveis_count,
                    'badge' => $produto->estoque->nome ?? 'Sem estoque',
                    'badge_variant' => 'secondary',
                ];
            })->all(),
        ];
    }
}
