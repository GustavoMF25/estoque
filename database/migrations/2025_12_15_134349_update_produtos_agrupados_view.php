<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $this->replaceView(<<<SQL
        CREATE VIEW produtos_agrupados_view AS
        WITH ultima_mov AS (
            SELECT
                produto_id,
                tipo,
                ROW_NUMBER() OVER (PARTITION BY produto_id ORDER BY id DESC) AS rn
            FROM movimentacoes
        )
        SELECT
            p.estoque_id,
            um.tipo AS ultima_movimentacao,
            p.nome,
            COUNT(p.id) AS quantidade_produtos,
            MAX(p.codigo_barras) AS codigo_barras,
            MAX(p.imagem) AS imagem,
            MAX(p.preco) AS preco,
            MAX(p.created_at) AS data_criacao,
            MAX(e.nome) AS estoque_nome,
            MAX(f.nome) AS fabricante_nome
        FROM produtos p
        LEFT JOIN estoques e ON p.estoque_id = e.id
        LEFT JOIN fabricantes f ON p.fabricante_id = f.id
        LEFT JOIN ultima_mov um ON p.id = um.produto_id AND um.rn = 1
        WHERE p.deleted_at IS NULL
        GROUP BY
            p.estoque_id,
            um.tipo,
            p.nome
        SQL);
    }

    public function down(): void
    {
        $this->replaceView(<<<SQL
        CREATE VIEW produtos_agrupados_view AS
        WITH ultima_mov AS (
            SELECT
                produto_id,
                tipo,
                ROW_NUMBER() OVER (PARTITION BY produto_id ORDER BY id DESC) AS rn
            FROM movimentacoes
        )
        SELECT
            p.estoque_id,
            um.tipo AS ultima_movimentacao,
            p.nome,
            COUNT(p.id) AS quantidade_produtos,
            MAX(p.codigo_barras) AS codigo_barras,
            MAX(p.imagem) AS imagem,
            MAX(p.preco) AS preco,
            MAX(p.created_at) AS data_criacao,
            MAX(e.nome) AS estoque_nome
        FROM produtos p
        LEFT JOIN estoques e ON p.estoque_id = e.id
        LEFT JOIN ultima_mov um ON p.id = um.produto_id AND um.rn = 1
        WHERE p.deleted_at IS NULL
        GROUP BY
            p.estoque_id,
            um.tipo,
            p.nome
        SQL);
    }

    private function replaceView(string $createSql): void
    {
        DB::statement('DROP VIEW IF EXISTS produtos_agrupados_view');
        DB::statement($createSql);
    }
};
