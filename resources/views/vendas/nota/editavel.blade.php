<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
        }
        .pagina {
            page-break-after: always;
            width: 210mm;
            height: 297mm;
            padding: 15mm;
            box-sizing: border-box;
            overflow: hidden;
        }
        .pagina-ultima {
            page-break-after: avoid;
        }
        .container {
            border: 1px solid #000;
            padding: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }
        .documento {
            text-align: center;
            border: 1px solid #000;
            padding: 4px;
            background: #f2f2f2;
            font-weight: bold;
        }
        .secao {
            border: 1px solid #000;
            margin-top: 5px;
        }
        .secao-titulo {
            background: #f2f2f2;
            font-weight: bold;
            padding: 2px 5px;
            border-bottom: 1px solid #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 3px 5px;
            border: 1px solid #000;
        }
        th {
            background: #f8f8f8;
            text-align: left;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .total {
            font-size: 12px;
            font-weight: bold;
        }
        .assinatura {
            margin-top: 40px;
            text-align: center;
        }
        .assinatura div {
            border-top: 1px solid #000;
            width: 220px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 10px;
        }
        .footer {
            font-size: 9px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    @if (!($apenasVerso ?? false))
        <div class="pagina">
            {!! $emissao->conteudo_frente !!}
        </div>
    @endif
    <div class="pagina pagina-ultima">
        {!! $emissao->conteudo_verso !!}
    </div>
</body>
</html>
