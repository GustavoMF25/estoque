<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\Notificacao;
use App\Models\Produto;
use App\Models\ProdutoChegada;
use App\Models\ProdutosUnidades;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $perfil = $user->perfil;

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $notificacoesNaoLidas = Notificacao::query()
            ->where('user_id', $user->id)
            ->whereNull('lida_em')
            ->count();

        return match ($perfil) {
            'admin', 'gerente' => $this->adminGerenteDashboard($today, $monthStart, $notificacoesNaoLidas, $perfil),
            'operador' => $this->operadorDashboard($today, $notificacoesNaoLidas),
            'vendedor' => $this->vendedorDashboard($user->id, $today, $monthStart, $notificacoesNaoLidas),
            default => view('dashboard', [
                'perfil' => $perfil,
                'cards' => [],
                'insignias' => [],
                'titulo' => 'Painel',
            ]),
        };
    }

    private function adminGerenteDashboard(Carbon $today, Carbon $monthStart, int $notificacoesNaoLidas, string $perfil)
    {
        $vendasHoje = Venda::query()
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelada')
            ->count();

        $faturamentoMes = (float) Venda::query()
            ->where('created_at', '>=', $monthStart)
            ->where('status', '!=', 'cancelada')
            ->sum(DB::raw('COALESCE(valor_final, valor_total)'));

        $aprovacoesPendentes = Venda::query()
            ->where('aprovacao_status', 'pendente')
            ->count();

        $estoquesAtivos = Estoque::query()
            ->where('status', 'ativo')
            ->count();

        $itensAChegarDisponiveis = (int) ProdutoChegada::query()
            ->where('status', 'aberto')
            ->sum(DB::raw('quantidade_total - quantidade_comprometida'));

        $lotesAtrasados = ProdutoChegada::query()
            ->where('status', 'aberto')
            ->whereDate('previsao_chegada', '<', $today)
            ->count();

        $insignias = [];
        if ($vendasHoje >= 10) {
            $insignias[] = ['texto' => 'Ritmo Forte de Vendas', 'classe' => 'badge-success'];
        }
        if ($aprovacoesPendentes > 0) {
            $insignias[] = ['texto' => 'Aprovações Pendentes', 'classe' => 'badge-warning'];
        }
        if ($lotesAtrasados > 0) {
            $insignias[] = ['texto' => 'Reposição Atrasada', 'classe' => 'badge-danger'];
        }
        if ($notificacoesNaoLidas === 0) {
            $insignias[] = ['texto' => 'Caixa de Entrada em Dia', 'classe' => 'badge-info'];
        }

        $cards = [
            ['titulo' => 'Vendas Hoje', 'valor' => $vendasHoje, 'tipo' => 'numero', 'icone' => 'fa-shopping-cart', 'cor' => 'primary'],
            ['titulo' => 'Faturamento do Mês', 'valor' => $faturamentoMes, 'tipo' => 'moeda', 'icone' => 'fa-dollar-sign', 'cor' => 'success'],
            ['titulo' => 'Aprovações Pendentes', 'valor' => $aprovacoesPendentes, 'tipo' => 'numero', 'icone' => 'fa-user-check', 'cor' => 'warning'],
            ['titulo' => 'Estoques Ativos', 'valor' => $estoquesAtivos, 'tipo' => 'numero', 'icone' => 'fa-warehouse', 'cor' => 'info'],
            ['titulo' => 'Itens a Chegar', 'valor' => $itensAChegarDisponiveis, 'tipo' => 'numero', 'icone' => 'fa-truck', 'cor' => 'secondary'],
            ['titulo' => 'Notificações Não Lidas', 'valor' => $notificacoesNaoLidas, 'tipo' => 'numero', 'icone' => 'fa-bell', 'cor' => 'dark'],
        ];

        return view('dashboard', [
            'perfil' => $perfil,
            'titulo' => 'Painel de Gestão',
            'cards' => $cards,
            'insignias' => $insignias,
        ]);
    }

    private function operadorDashboard(Carbon $today, int $notificacoesNaoLidas)
    {
        $estoquesAtivos = Estoque::query()->where('status', 'ativo')->count();
        $unidadesDisponiveis = ProdutosUnidades::query()->where('status', 'disponivel')->count();

        $lotesAbertos = ProdutoChegada::query()->where('status', 'aberto')->count();
        $itensAChegarDisponiveis = (int) ProdutoChegada::query()
            ->where('status', 'aberto')
            ->sum(DB::raw('quantidade_total - quantidade_comprometida'));

        $lotesAtrasados = ProdutoChegada::query()
            ->where('status', 'aberto')
            ->whereDate('previsao_chegada', '<', $today)
            ->count();

        $estoquesComCapacidadeCritica = DB::table('estoques as e')
            ->leftJoin('produtos as p', 'p.estoque_id', '=', 'e.id')
            ->leftJoin('produtos_unidades as pu', 'pu.produto_id', '=', 'p.id')
            ->whereNull('p.deleted_at')
            ->where('e.quantidade_maxima', '>', 0)
            ->select('e.id', 'e.quantidade_maxima')
            ->groupBy('e.id', 'e.quantidade_maxima')
            ->havingRaw('COUNT(pu.id) >= (e.quantidade_maxima * 0.8)')
            ->get()
            ->count();

        $insignias = [];
        if ($lotesAtrasados === 0) {
            $insignias[] = ['texto' => 'Reposição em Dia', 'classe' => 'badge-success'];
        }
        if ($estoquesComCapacidadeCritica > 0) {
            $insignias[] = ['texto' => 'Capacidade Crítica', 'classe' => 'badge-danger'];
        }
        if ($notificacoesNaoLidas === 0) {
            $insignias[] = ['texto' => 'Sem Pendências de Notificação', 'classe' => 'badge-info'];
        }

        $cards = [
            ['titulo' => 'Estoques Ativos', 'valor' => $estoquesAtivos, 'tipo' => 'numero', 'icone' => 'fa-warehouse', 'cor' => 'primary'],
            ['titulo' => 'Unidades Disponíveis', 'valor' => $unidadesDisponiveis, 'tipo' => 'numero', 'icone' => 'fa-boxes', 'cor' => 'success'],
            ['titulo' => 'Lotes a Chegar (Abertos)', 'valor' => $lotesAbertos, 'tipo' => 'numero', 'icone' => 'fa-truck-loading', 'cor' => 'warning'],
            ['titulo' => 'Itens a Chegar', 'valor' => $itensAChegarDisponiveis, 'tipo' => 'numero', 'icone' => 'fa-truck', 'cor' => 'secondary'],
            ['titulo' => 'Lotes Atrasados', 'valor' => $lotesAtrasados, 'tipo' => 'numero', 'icone' => 'fa-exclamation-triangle', 'cor' => 'danger'],
            ['titulo' => 'Notificações Não Lidas', 'valor' => $notificacoesNaoLidas, 'tipo' => 'numero', 'icone' => 'fa-bell', 'cor' => 'dark'],
        ];

        return view('dashboard', [
            'perfil' => 'operador',
            'titulo' => 'Painel Operacional',
            'cards' => $cards,
            'insignias' => $insignias,
        ]);
    }

    private function vendedorDashboard(int $userId, Carbon $today, Carbon $monthStart, int $notificacoesNaoLidas)
    {
        $minhasVendasHoje = Venda::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelada')
            ->count();

        $meuFaturamentoMes = (float) Venda::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $monthStart)
            ->where('status', '!=', 'cancelada')
            ->sum(DB::raw('COALESCE(valor_final, valor_total)'));

        $minhasPendenciasAprovacao = Venda::query()
            ->where('user_id', $userId)
            ->where('aprovacao_status', 'pendente')
            ->count();

        $clientesAtivos = Cliente::query()->where('ativo', true)->count();
        $produtosAtivos = Produto::query()->where('ativo', true)->count();

        $ticketMedioMes = (float) Venda::query()
            ->where('user_id', $userId)
            ->where('created_at', '>=', $monthStart)
            ->where('status', '!=', 'cancelada')
            ->avg(DB::raw('COALESCE(valor_final, valor_total)'));

        $insignias = [];
        if ($minhasVendasHoje >= 5) {
            $insignias[] = ['texto' => 'Vendedor em Destaque Hoje', 'classe' => 'badge-success'];
        }
        if ($minhasPendenciasAprovacao > 0) {
            $insignias[] = ['texto' => 'Aguardando Aprovação', 'classe' => 'badge-warning'];
        }
        if ($notificacoesNaoLidas === 0) {
            $insignias[] = ['texto' => 'Comunicação em Dia', 'classe' => 'badge-info'];
        }

        $cards = [
            ['titulo' => 'Minhas Vendas Hoje', 'valor' => $minhasVendasHoje, 'tipo' => 'numero', 'icone' => 'fa-shopping-bag', 'cor' => 'primary'],
            ['titulo' => 'Meu Faturamento no Mês', 'valor' => $meuFaturamentoMes, 'tipo' => 'moeda', 'icone' => 'fa-wallet', 'cor' => 'success'],
            ['titulo' => 'Ticket Médio (Mês)', 'valor' => $ticketMedioMes, 'tipo' => 'moeda', 'icone' => 'fa-chart-line', 'cor' => 'info'],
            ['titulo' => 'Vendas Pendentes', 'valor' => $minhasPendenciasAprovacao, 'tipo' => 'numero', 'icone' => 'fa-hourglass-half', 'cor' => 'warning'],
            ['titulo' => 'Clientes Ativos', 'valor' => $clientesAtivos, 'tipo' => 'numero', 'icone' => 'fa-users', 'cor' => 'secondary'],
            ['titulo' => 'Produtos no Catálogo', 'valor' => $produtosAtivos, 'tipo' => 'numero', 'icone' => 'fa-tags', 'cor' => 'dark'],
        ];

        return view('dashboard', [
            'perfil' => 'vendedor',
            'titulo' => 'Painel de Vendas',
            'cards' => $cards,
            'insignias' => $insignias,
        ]);
    }
}
