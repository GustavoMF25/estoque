<?php

namespace App\Livewire\Vendas;

use App\Helpers\FormatHelper;
use App\Models\Cliente;
use App\Models\NotaEmissao;
use App\Models\NotaModelo;
use App\Models\Venda;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EmitirNota extends Component
{
    public $vendaId;
    public $venda;
    public $modelos = [];
    public $clientes = [];

    public $modelo_id;
    public $cliente_id;

    public $cliente_nome;
    public $cliente_documento;
    public $cliente_email;
    public $cliente_telefone;

    public $cep;
    public $rua;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $estado;

    public $previewFrente = '';
    public $previewVerso = '';
    public $modeloIcone = '';

    public function mount($vendaId)
    {
        $this->vendaId = $vendaId;
        $this->venda = Venda::with(['itens.produto', 'cliente.enderecoPadrao'])->findOrFail($vendaId);
        $this->modelos = NotaModelo::where('ativo', true)->orderBy('nome')->get();
        $this->clientes = Cliente::orderBy('nome')->get();

        if ($this->venda->cliente) {
            $this->cliente_id = $this->venda->cliente_id;
            $this->preencherCliente($this->venda->cliente);
        }

        if ($this->modelos->count() === 1) {
            $this->modelo_id = $this->modelos->first()->id;
        }

        $this->atualizarPreview();
    }

    public function updated($field)
    {
        if ($field === 'cliente_id') {
            if ($this->cliente_id) {
                $cliente = Cliente::with('enderecoPadrao')->find($this->cliente_id);
                if ($cliente) {
                    $this->preencherCliente($cliente);
                }
            } else {
                $this->cliente_nome = '';
                $this->cliente_documento = '';
                $this->cliente_email = '';
                $this->cliente_telefone = '';
                $this->cep = '';
                $this->rua = '';
                $this->numero = '';
                $this->complemento = '';
                $this->bairro = '';
                $this->cidade = '';
                $this->estado = '';
            }
        }

        $this->atualizarPreview();
    }

    protected function preencherCliente(Cliente $cliente): void
    {
        $this->cliente_nome = $cliente->nome;
        $this->cliente_documento = $cliente->documento;
        $this->cliente_email = $cliente->email;
        $this->cliente_telefone = $cliente->telefone;

        $endereco = $cliente->enderecoPadrao;
        if ($endereco) {
            $this->cep = $endereco->cep;
            $this->rua = $endereco->rua;
            $this->numero = $endereco->numero;
            $this->complemento = $endereco->complemento;
            $this->bairro = $endereco->bairro;
            $this->cidade = $endereco->cidade;
            $this->estado = $endereco->estado;
        }
    }

    protected function montarTabelaItens(): string
    {
        $linhas = '';
        foreach ($this->venda->itens as $item) {
            $linhas .= '<tr>'
                . '<td>' . e($item->produto->nome ?? '-') . '</td>'
                . '<td style="text-align:center;">' . e($item->quantidade) . '</td>'
                . '<td style="text-align:right;">' . e(FormatHelper::brl($item->valor_unitario)) . '</td>'
                . '<td style="text-align:right;">' . e(FormatHelper::brl($item->valor_total)) . '</td>'
                . '</tr>';
        }

        return '<table style="width:100%; border-collapse:collapse;" border="1" cellpadding="6">'
            . '<thead><tr>'
            . '<th>Produto</th><th>Qtde</th><th>Vlr. Unitário</th><th>Vlr. Total</th>'
            . '</tr></thead><tbody>'
            . $linhas
            . '</tbody></table>';
    }

    protected function dadosTemplate(): array
    {
        return [
            '{{cliente_nome}}' => e($this->cliente_nome ?? ''),
            '{{cliente_documento}}' => e($this->cliente_documento ?? ''),
            '{{cliente_email}}' => e($this->cliente_email ?? ''),
            '{{cliente_telefone}}' => e($this->cliente_telefone ?? ''),
            '{{endereco_cep}}' => e($this->cep ?? ''),
            '{{endereco_rua}}' => e($this->rua ?? ''),
            '{{endereco_numero}}' => e($this->numero ?? ''),
            '{{endereco_complemento}}' => e($this->complemento ?? ''),
            '{{endereco_bairro}}' => e($this->bairro ?? ''),
            '{{endereco_cidade}}' => e($this->cidade ?? ''),
            '{{endereco_estado}}' => e($this->estado ?? ''),
            '{{venda_id}}' => e($this->venda->id),
            '{{venda_protocolo}}' => e($this->venda->protocolo),
            '{{valor_total}}' => e(FormatHelper::brl($this->venda->valor_total)),
            '{{valor_final}}' => e(FormatHelper::brl($this->venda->valor_final ?? $this->venda->valor_total)),
            '{{data}}' => e(now()->format('d/m/Y')),
            '{{itens_tabela}}' => $this->montarTabelaItens(),
        ];
    }

    protected function renderTemplate(string $template): string
    {
        return strtr($template, $this->dadosTemplate());
    }

    public function atualizarPreview(): void
    {
        $modelo = $this->modelo_id ? NotaModelo::find($this->modelo_id) : null;
        if (!$modelo) {
            $this->previewFrente = '<div class="text-muted">Selecione um modelo para visualizar.</div>';
            $this->previewVerso = '';
            return;
        }

        $this->modeloIcone = $modelo->icone ?? '';
        $this->previewFrente = $this->renderTemplate($modelo->conteudo_frente);
        $this->previewVerso = view('vendas.nota._conteudo', ['venda' => $this->venda])->render();
    }

    public function emitir()
    {
        $this->validate([
            'modelo_id' => 'required|exists:nota_modelos,id',
            'cliente_nome' => 'nullable|string|max:255',
            'cliente_documento' => 'nullable|string|max:255',
            'cliente_email' => 'nullable|email|max:255',
            'cliente_telefone' => 'nullable|string|max:50',
        ]);

        return DB::transaction(function () {
            $clienteId = $this->cliente_id;

            if (!$clienteId && $this->cliente_nome) {
                $clienteExistente = null;
                if (!empty($this->cliente_email)) {
                    $clienteExistente = Cliente::where('email', $this->cliente_email)->first();
                }

                if ($clienteExistente) {
                    $clienteId = $clienteExistente->id;
                } else {
                    $cliente = Cliente::create([
                        'nome' => $this->cliente_nome,
                        'documento' => $this->cliente_documento,
                        'email' => $this->cliente_email,
                        'telefone' => $this->cliente_telefone,
                        'ativo' => true,
                    ]);
                    $clienteId = $cliente->id;
                    $this->cliente_id = $clienteId;
                }

                if ($clienteId && array_filter([$this->cep, $this->rua, $this->cidade, $this->estado])) {
                    $clienteRef = $clienteExistente ?? $cliente ?? Cliente::find($clienteId);
                    $clienteRef?->enderecos()->create([
                        'rotulo' => 'Principal',
                        'padrao' => true,
                        'cep' => $this->cep,
                        'rua' => $this->rua,
                        'numero' => $this->numero,
                        'complemento' => $this->complemento,
                        'bairro' => $this->bairro,
                        'cidade' => $this->cidade,
                        'estado' => $this->estado,
                    ]);
                }
            }

            if ($clienteId && $this->venda->cliente_id !== $clienteId) {
                $this->venda->update(['cliente_id' => $clienteId]);
                $this->venda->refresh()->load('cliente.enderecoPadrao');
            }

            $modelo = NotaModelo::findOrFail($this->modelo_id);

            $emissao = NotaEmissao::create([
                'venda_id' => $this->venda->id,
                'modelo_id' => $modelo->id,
                'cliente_id' => $clienteId,
                'user_id' => auth()->id(),
                'cliente_nome' => $this->cliente_nome,
                'cliente_documento' => $this->cliente_documento,
                'cliente_email' => $this->cliente_email,
                'cliente_telefone' => $this->cliente_telefone,
                'cep' => $this->cep,
                'rua' => $this->rua,
                'numero' => $this->numero,
                'complemento' => $this->complemento,
                'bairro' => $this->bairro,
                'cidade' => $this->cidade,
                'estado' => $this->estado,
                'conteudo_frente' => $this->renderTemplate($modelo->conteudo_frente),
                'conteudo_verso' => view('vendas.nota._conteudo', ['venda' => $this->venda])->render(),
            ]);

            return $this->redirect(route('vendas.nota.editavel', $emissao->id));
        });
    }

    public function render()
    {
        return view('livewire.vendas.emitir-nota');
    }
}
