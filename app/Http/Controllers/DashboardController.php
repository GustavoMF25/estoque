<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\ProdutoChegada;
use App\Models\ProdutosUnidades;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $perfil = auth()->user()->perfil;

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
        if ($lotesAtrasados > 0) {
            $insignias[] = ['texto' => 'Reposição Atrasada', 'classe' => 'badge-danger'];
        }
        if ($lotesAtrasados === 0) {
            $insignias[] = ['texto' => 'Reposição em Dia', 'classe' => 'badge-success'];
        }

        $unidadesDisponiveis = ProdutosUnidades::query()->where('status', 'disponivel')->count();
        $unidadesVendidas = ProdutosUnidades::query()->where('status', 'vendido')->count();
        $lotesAbertos = ProdutoChegada::query()->where('status', 'aberto')->count();

        $estoquesComCapacidadeCritica = (int) DB::table('estoques as e')
            ->leftJoin('produtos as p', 'p.estoque_id', '=', 'e.id')
            ->leftJoin('produtos_unidades as pu', 'pu.produto_id', '=', 'p.id')
            ->whereNull('p.deleted_at')
            ->where('e.quantidade_maxima', '>', 0)
            ->select('e.id', 'e.quantidade_maxima')
            ->groupBy('e.id', 'e.quantidade_maxima')
            ->havingRaw('COUNT(pu.id) >= (e.quantidade_maxima * 0.8)')
            ->count();

        if ($estoquesComCapacidadeCritica > 0) {
            $insignias[] = ['texto' => 'Capacidade Crítica', 'classe' => 'badge-danger'];
        }

        $cards = [
            ['titulo' => 'Estoques Ativos', 'valor' => $estoquesAtivos, 'tipo' => 'numero', 'icone' => 'fa-warehouse', 'cor' => 'primary'],
            ['titulo' => 'Unidades Disponíveis', 'valor' => $unidadesDisponiveis, 'tipo' => 'numero', 'icone' => 'fa-boxes', 'cor' => 'success'],
            ['titulo' => 'Unidades Vendidas', 'valor' => $unidadesVendidas, 'tipo' => 'numero', 'icone' => 'fa-tags', 'cor' => 'info'],
            ['titulo' => 'Lotes a Chegar (Abertos)', 'valor' => $lotesAbertos, 'tipo' => 'numero', 'icone' => 'fa-truck-loading', 'cor' => 'warning'],
            ['titulo' => 'Itens a Chegar', 'valor' => $itensAChegarDisponiveis, 'tipo' => 'numero', 'icone' => 'fa-truck', 'cor' => 'secondary'],
            ['titulo' => 'Lotes Atrasados', 'valor' => $lotesAtrasados, 'tipo' => 'numero', 'icone' => 'fa-exclamation-triangle', 'cor' => 'danger'],
        ];

        return view('dashboard', [
            'perfil' => $perfil,
            'titulo' => 'Painel de Estoque',
            'cards' => $cards,
            'insignias' => $insignias,
        ]);
    }
}
