<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fatura;
use Carbon\Carbon;

class WebhookMercadoPagoController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        // Identifica o tipo de evento
        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];

            // Busca detalhes da transação
            $ch = curl_init("https://api.mercadopago.com/v1/payments/$paymentId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . env('MERCADO_PAGO_ACCESS_TOKEN')
            ]);
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if ($response && isset($response['external_reference'])) {
                $faturaId = $response['external_reference'];
                $status = $response['status'];

                $fatura = Fatura::find($faturaId);
                if ($fatura && $status === 'approved') {
                    // 🔹 Atualiza fatura e renova assinatura
                    $fatura->update([
                        'status' => 'pago',
                        'data_pagamento' => now(),
                    ]);

                    $assinatura = $fatura->assinatura;
                    if ($assinatura) {
                        $dataBase = $fatura->data_vencimento
                            ? Carbon::parse($fatura->data_vencimento)
                            : now();

                        $periodicidade = $assinatura->periodicidade ?? 'mensal';
                        $novaDataVencimento = match ($periodicidade) {
                            'trimestral' => $dataBase->copy()->addMonths(3),
                            'anual' => $dataBase->copy()->addYear(),
                            'vitalicio' => null,
                            default => $dataBase->copy()->addMonth(),
                        };

                        $assinatura->update([
                            'status' => 'ativo',
                            'data_vencimento' => $novaDataVencimento,
                            'em_teste' => false,
                            'trial_expira_em' => null,
                            'ultima_confirmacao' => now(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
