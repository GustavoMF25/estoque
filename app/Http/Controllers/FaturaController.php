<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\Assinaturas;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Preference;

class FaturaController extends Controller
{
    /**
     * Exibe todas as faturas de uma assinatura.
     */
    public function index($assinaturaId)
    {
        $assinatura = Assinaturas::with('empresa')->findOrFail($assinaturaId);
        $faturas = Fatura::where('assinatura_id', $assinaturaId)
            ->orderByDesc('data_vencimento')
            ->get();

        return view('faturas.index', compact('assinatura', 'faturas'));
    }

    /**
     * Formulário de criação de nova fatura.
     */
    public function create($assinaturaId)
    {
        $assinatura = Assinaturas::with('empresa')->findOrFail($assinaturaId);
        return view('faturas.create', compact('assinatura'));
    }

    /**
     * Salva uma nova fatura no banco de dados.
     */
    public function store(Request $request, $assinaturaId)
    {
        try {
            $assinatura = Assinaturas::with('empresa')->findOrFail($assinaturaId);

            $request->validate([
                'valor' => 'required|numeric|min:0',
                'data_vencimento' => 'required|date',
                'observacoes' => 'nullable|string|max:500',
            ]);

            // Código único da fatura (ex: FAT-2025-ABC123)
            $codigo = 'FAT-' . now()->format('Y') . '-' . strtoupper(Str::random(6));

            $fatura = Fatura::create([
                'assinatura_id' => $assinatura->id,
                'empresa_id' => $assinatura->empresa_id,
                'codigo' => $codigo,
                'valor' => $request->valor,
                'data_vencimento' => $request->data_vencimento,
                'status' => 'pendente',
                'observacoes' => $request->observacoes,
            ]);

            // 🔹 Gera o link de pagamento no Mercado Pago
            MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));

            // Criação da preference (link de pagamento)
            $client = new PreferenceClient(); // Usando PreferenceClient
            $preference = $client->create([
                "items" => [
                    [
                        "title" => "Fatura {$fatura->codigo}",
                        "quantity" => 1,
                        "unit_price" => (float) $fatura->valor,
                        "currency_id" => "BRL",
                    ]
                ],
                "payer" => [
                    "email" => $assinatura->empresa->email ?? 'cliente@exemplo.com',
                ],
                "external_reference" => (string) $fatura->id,
                "notification_url" => env('MERCADO_PAGO_WEBHOOK_URL'),

                // // ✅ Definir os back_urls corretamente
                "back_urls" => [
                    "success" => route('faturas.pagamento.sucesso', $fatura->id), // URL de sucesso
                    "failure" => route('faturas.pagamento.erro', $fatura->id),    // URL de falha
                ],

                "auto_return" => "approved", // Isso permite o retorno automático para sucesso
            ]);
            // Salva o link de pagamento na fatura
            $fatura->update([
                'link_pagamento' => $preference->init_point, // Link gerado
            ]);

            // $this->enviarLinkPagamentoWhatsApp($fatura->id);

            return redirect()
                ->route('assinaturas.show', $assinatura->id)
                ->with('success', 'Fatura criada com sucesso!');
        } catch (MPApiException $e) {
            echo "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
            echo "Content: ";
            var_dump($e->getApiResponse()->getContent());
            echo "\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Marca uma fatura como paga.
     */
    public function marcarPago($id)
    {
        $fatura = Fatura::with('assinatura')->findOrFail($id);

        // Atualiza status e data de pagamento
        $fatura->update([
            'status' => 'pago',
            'data_pagamento' => now(),
        ]);

        $assinatura = $fatura->assinatura;

        if ($assinatura) {
            // 🔹 Base da data = vencimento da fatura (ou hoje, se nulo)
            $dataBase = $fatura->data_vencimento
                ? \Carbon\Carbon::parse($fatura->data_vencimento)
                : \Carbon\Carbon::now();

            // 🔹 Escolhe o período (fixo mensal se não tiver periodicidade)
            $periodo = $assinatura->periodicidade ?? 'mensal';

            switch ($periodo) {
                case 'trimestral':
                    $novaDataExpiracao = $dataBase->copy()->addMonths(3);
                    break;
                case 'anual':
                    $novaDataExpiracao = $dataBase->copy()->addYear();
                    break;
                default:
                    $novaDataExpiracao = $dataBase->copy()->addMonth();
                    break;
            }

            $assinatura->update([
                'status' => 'ativo',
                'data_vencimento' => $novaDataExpiracao,
            ]);
        }

        return redirect()
            ->route('assinaturas.show', $assinatura->id)
            ->with('success', 'Fatura marcada como paga!');
    }

    /**
     * (Opcional) Exibe detalhes de uma fatura individual.
     */
    public function show($id)
    {
        $fatura = Fatura::with(['assinatura.empresa'])->findOrFail($id);
        return view('faturas.show', compact('fatura'));
    }

    /**
     * Exclui uma fatura (opcional, controle administrativo).
     */
    public function destroy($id)
    {
        $fatura = Fatura::findOrFail($id);
        $fatura->delete();

        return redirect()->back()->with('success', 'Fatura removida com sucesso!');
    }

    public function sucesso($id)
    {
        $fatura = Fatura::findOrFail($id);

        return view('faturas.pagamento-sucesso', compact('fatura'));
    }

    public function erro($id)
    {
        $fatura = Fatura::findOrFail($id);

        return view('faturas.pagamento-erro', compact('fatura'));
    }

    public function enviarLinkPagamentoWhatsApp($faturaId)
    {
        // Busque a fatura criada
        $fatura = Fatura::findOrFail($faturaId);

        // Obtenha o número de WhatsApp do cliente (supondo que tenha esse campo)
        $cliente = $fatura->cliente;
        $numeroWhatsapp = $cliente->whatsapp; // A linha aqui depende de como você armazena o número de WhatsApp

        // Mensagem para enviar
        $mensagem = "Olá, seu link para pagamento está pronto! Acesse: " . $fatura->link_pagamento;

        // Enviar o link de pagamento via WhatsApp
        $whatsappService = new WhatsAppService();
        $whatsappService->enviarMensagem($numeroWhatsapp, $mensagem);

        return response()->json(['status' => 'Mensagem enviada com sucesso!']);
    }
}
