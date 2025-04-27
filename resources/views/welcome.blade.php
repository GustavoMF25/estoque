<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Estoques</title>

    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(to right, #0f172a, #1e293b);
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        .buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .buttons a {
            padding: 0.75rem 1.5rem;
            background-color: #10b981;
            color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover {
            background-color: #059669;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="title">
            Sistema de Gestão de Estoques
        </div>
        <div class="subtitle">
            Bem-vindo! Administre produtos, controle estoques de múltiplas lojas, monitore movimentações e facilite suas vendas de maneira ágil e segura.
        </div>

        <div class="buttons">
            @auth
                <a href="{{ url('/dashboard') }}">Acessar Painel</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Registrar-se</a>
                @endif
            @endauth
        </div>
    </div>
</body>
</html>
