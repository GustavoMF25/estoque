<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class VendaController extends Controller
{
    public function gerar($vendaId)
    {
        $venda = Venda::with(['itens.produto', 'usuario', 'loja'])->findOrFail($vendaId);

        $html = view('vendas.nota.pdf', compact('venda'))->render();

        // return $html;
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output("Venda_{$venda->protocolo}.pdf", 'I'); // 'I' abre no navegador, 'D' força download
    }
}
