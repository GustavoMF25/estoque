{{-- resources/views/landing.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestão de Estoques</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">


    <style>
        :root {
            --bg: #1b2417;
            /* fundo geral (versão escura do #2c3a28) */
            --card: #2c3a28;
            /* cor principal da paleta */
            --border: #3c4b36;
            /* bordas e divisórias */
            --fg: #f0f5ec;
            /* texto principal */
            --muted: #bfc8b7;
            /* texto secundário */
            --primary: #8bc34a;
            /* verde-claro de destaque */
            --primary-fore: #1b2417;
            --accent: #a4d36e;
            /* tom auxiliar */
            --container: 1200px;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(0, 0, 0, .35);
            --shadow-elegant: 0 20px 60px rgba(30, 40, 30, .35);
            --transition: 220ms cubic-bezier(.2, .8, .2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            background: var(--bg);
            color: var(--fg);
            font-family: "Inter", system-ui, Segoe UI, Roboto, Arial, sans-serif;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: color var(--transition)
        }

        img,
        svg {
            display: inline-block
        }

        .container {
            max-width: var(--container);
            margin: 0 auto;
            padding: 0 1rem
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .85rem 1.2rem;
            border-radius: 12px;
            border: 1px solid transparent;
            background: var(--primary);
            color: var(--primary-fore);
            font-weight: 600;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: transform var(--transition), filter var(--transition);
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.05)
        }

        .btn-outline {
            background: transparent;
            color: var(--fg);
            border-color: var(--border)
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, .05)
        }

        .btn-ghost {
            background: transparent;
            color: var(--fg)
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, .05)
        }

        .shadow-elegant {
            box-shadow: var(--shadow-elegant)
        }

        .glass {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
            backdrop-filter: saturate(140%) blur(10px);
            -webkit-backdrop-filter: saturate(140%) blur(10px);
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .transition-smooth {
            transition: all var(--transition)
        }

        /* Gradientes */
        .gradient-dark {
            background:
                radial-gradient(1000px 500px at 10% -10%, rgba(139, 195, 74, .18), transparent 60%),
                radial-gradient(900px 500px at 110% 110%, rgba(164, 211, 110, .12), transparent 60%),
                linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .5) 100%);
        }

        .gradient-primary {
            background-image: linear-gradient(90deg, #9fd86a, #c2f07e);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .gradient-accent {
            background:
                radial-gradient(800px 400px at 0% 100%, rgba(164, 211, 110, .08), transparent 60%),
                radial-gradient(800px 400px at 100% 0%, rgba(139, 195, 74, .08), transparent 60%);
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            border-bottom: 1px solid rgba(255, 255, 255, .06)
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0
        }

        .nav-links {
            display: flex;
            gap: 2rem
        }

        .nav-links a:hover {
            color: var(--primary)
        }

        .nav-mobile-btn {
            display: none
        }

        .nav-mobile {
            display: none
        }

        @media(max-width:768px) {
            .nav-links {
                display: none
            }

            .nav-mobile-btn {
                display: inline-flex
            }

            .nav-mobile {
                display: block;
                padding: .75rem 0
            }

            .nav-mobile a {
                display: block;
                padding: .75rem 0;
                border-top: 1px solid rgba(255, 255, 255, .05)
            }
        }

        .hero {
            position: relative;
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding-top: 5rem
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            line-height: 1.05;
            margin-bottom: .6rem
        }

        .hero p {
            color: var(--muted);
            font-size: clamp(1rem, 2.2vw, 1.25rem);
            max-width: 42rem;
            margin: 0 auto 1.5rem;
            text-align: center
        }

        .section {
            padding: 6rem 0
        }

        .section h2 {
            font-size: clamp(1.8rem, 4.5vw, 3rem);
            margin-bottom: .5rem;
            text-align: center
        }

        .section p.lead {
            color: var(--muted);
            font-size: 1.1rem;
            text-align: center;
            max-width: 36rem;
            margin: 0 auto 2rem
        }

        .grid {
            display: grid;
            gap: 1rem
        }

        @media(min-width:768px) {
            .grid-2 {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(min-width:1024px) {
            .grid-3 {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        .feature-card {
            padding: 1.25rem;
            border-radius: var(--radius)
        }

        .feature-icon {
            display: inline-flex;
            padding: .75rem;
            border-radius: 14px;
            background: rgba(139, 195, 74, .10);
            margin-bottom: .75rem;
            transition: background var(--transition), transform var(--transition)
        }

        .feature-card:hover {
            border-color: rgba(139, 195, 74, .5);
            box-shadow: var(--shadow-elegant);
            transform: translateY(-2px)
        }

        .feature-card:hover .feature-icon {
            background: rgba(139, 195, 74, .18)
        }

        .price-card {
            position: relative;
            border-radius: var(--radius);
            transition: transform var(--transition)
        }

        .price-card:hover {
            transform: translateY(-4px)
        }

        .badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: var(--primary-fore);
            padding: .35rem .75rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .8rem;
            box-shadow: var(--shadow)
        }

        .price-head {
            text-align: center;
            padding: 2rem 1rem 1rem
        }

        .price-features {
            list-style: none;
            padding: 0 1.25rem 1.25rem;
            margin: 0
        }

        .price-features li {
            display: flex;
            gap: .5rem;
            align-items: flex-start;
            margin: .55rem 0;
            color: var(--fg)
        }

        footer {
            border-top: 1px solid var(--border)
        }

        .footer-cols {
            display: grid;
            gap: 2rem
        }

        @media(min-width:768px) {
            .footer-cols {
                grid-template-columns: 2fr 1fr 1fr
            }
        }

        .muted {
            color: var(--muted)
        }

        .link:hover {
            color: var(--primary)
        }

        .center {
            text-align: center
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="nav glass-effect" style="border-radius: 0px;">
        <div class="container">
            <div class="nav-row">
                <!-- Marca -->
                <div class="brand">
                    <img src="{{ asset('imagens/icon.png') }}" width="100" alt="Logo" class="brand-image"
                        style="opacity:.8">
                </div>



                <!-- Links principais -->
                <div class="nav-links" id="navLinks">
                    <a href="#recursos" class="link">Recursos</a>
                    <a href="#planos" class="link">Planos</a>
                    <a href="#contato" class="link">Contato</a>
                </div>

                <div class="nav-row">
                    <!-- Botões de ação -->
                    <div class="nav-cta">
                        <a href="{{ route('login') }}" class="btn btn-ghost">Entrar</a>
                        <a href="#planos" class="btn btn-primary shadow-elegant">Começar Grátis</a>
                    </div>
                    <!-- Botão de menu (hambúrguer) -->
                    <button class="nav-toggle" id="navToggle" aria-label="Abrir menu">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero gradient-dark">
        <div class="bg-bubble bubble-a"></div>
        <div class="bg-bubble bubble-b"></div>

        <div class="container hero-inner">
            <div class="floating-icons">
                {{-- <div class="chip glass-effect"> --}}
                <img src="{{ asset('imagens/icon.png') }}" width="300" alt="Logo"
                    class="brand-image "style="opacity: .8">
                {{-- </div> --}}
                {{-- <div class="chip glass-effect"><i data-lucide="store"></i></div>
                <div class="chip glass-effect"><i data-lucide="trending-up"></i></div> --}}
            </div>

            <h1 class="hero-title">
                Sistema de Gestão de
                <span class="text-gradient">Estoques</span>
            </h1>

            <p class="hero-sub">
                Administre produtos, controle estoques de múltiplas lojas, monitore movimentações e
                facilite suas vendas de maneira ágil e segura.
            </p>

            <div class="hero-actions">
                <a href="#planos" class="btn btn-primary shadow-elegant btn-lg">
                    Começar Agora <i data-lucide="arrow-right" class="ml"></i>
                </a>
                <a href="#demo" class="btn btn-outline btn-lg">Ver Demonstração</a>
            </div>

            <div class="stats">
                <div class="stat glass-effect">
                    <div class="stat-number">99,9%</div>
                    <div class="stat-label">Disponibilidade do Sistema</div>
                </div>

                <div class="stat glass-effect">
                    <div class="stat-number">12h/dia</div>
                    <div class="stat-label">Suporte Rápido e Humanizado</div>
                </div>

                <div class="stat glass-effect">
                    <div class="stat-number">120+</div>
                    <div class="stat-label">Empresas Ativas</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="recursos" class="section">
        <div class="container">
            <div class="section-head">
                <h2>Recursos <span class="text-primary">Poderosos</span></h2>
                <p>Tudo que você precisa para gerenciar seu estoque de forma profissional e eficiente</p>
            </div>

            <div class="grid grid-3">
                <article class="card hover-elevate">
                    <div class="icon-badge"><i data-lucide="package"></i></div>
                    <h3>Gestão de Produtos</h3>
                    <p>Cadastre e gerencie seu catálogo completo com fotos, descrições e variações de produtos.</p>
                </article>

                <article class="card hover-elevate" style="position: relative;">
                    <div class="icon-badge"><i data-lucide="store"></i></div>
                    <h3>Múltiplas Lojas</h3>
                    <p>Controle o estoque de diferentes unidades e filiais em uma única plataforma centralizada.</p>

                    <div class="badge-dev">
                        EM DESENVOLVIMENTO
                    </div>
                </article>

                <article class="card hover-elevate" style="position: relative;">
                    <div class="icon-badge"><i data-lucide="bar-chart-3"></i></div>
                    <h3>Relatórios em Tempo Real</h3>
                    <p>Análises detalhadas sobre movimentações, vendas e níveis de estoque com dashboards interativos.
                    </p>

                    <div class="badge-dev">
                        EM DESENVOLVIMENTO
                    </div>
                </article>

                <article class="card hover-elevate">
                    <div class="icon-badge"><i data-lucide="truck"></i></div>
                    <h3>Controle de Movimentações</h3>
                    <p>Registre entradas, saídas, transferências entre lojas e mantenha histórico completo.</p>
                </article>

                <article class="card hover-elevate" style="position: relative;">
                    <div class="icon-badge"><i data-lucide="shopping-cart"></i></div>
                    <h3>Vendas e Notas Fiscais</h3>
                    <p>
                        Gerencie todo o processo de venda de forma prática — adicione produtos ao carrinho,
                        registre as transações e gere automaticamente notas fiscais <strong>DAFs</strong> com integração
                        direta ao sistema.
                    </p>

                    <div class="badge-dev">
                        EM DESENVOLVIMENTO
                    </div>
                </article>

                <article class="card hover-elevate" style="position: relative;">
                    <div class="icon-badge"><i data-lucide="zap"></i></div>
                    <h3>Alertas Inteligentes</h3>
                    <p>Notificações automáticas sobre estoque baixo, vencimentos e movimentações importantes.</p>

                    <div class="badge-dev">
                        EM DESENVOLVIMENTO
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section id="planos" class="section gradient-accent">
        <div class="container">
            <div class="section-head">
                <h2>Planos para seu <span class="text-primary">Crescimento</span></h2>
                <p>Escolha o plano ideal para o tamanho do seu negócio</p>
            </div>

            <div class="grid grid-1 pricing" style="max-width:700px; margin:0 auto;">
                <article class="price-card featured">
                    <div class="ribbon">Acesso Completo</div>
                    <div class="price-head">
                        <h3>Plano Profissional</h3>
                        <p>Experimente todos os recursos na versão Beta</p>
                        <div class="price-line"><span>R$ 200</span><small>/mês</small></div>
                    </div>
                    <ul class="price-list">
                        <li><i data-lucide="check"></i> Cadastro e controle de produtos</li>
                        <li><i data-lucide="check"></i> Gestão de estoque e movimentações</li>
                        <li><i data-lucide="check"></i> Registro de vendas e notas DAFs</li>
                        <li><i data-lucide="check"></i> Relatórios e histórico de transações</li>
                        <li><i data-lucide="check"></i> Múltiplas lojas e usuários</li>
                        <li><i data-lucide="check"></i> Suporte via WhatsApp e e-mail</li>
                    </ul>
                    <a href="https://wa.me/5521974332531?text=Olá!%20Gostaria%20de%20testar%20o%20Plano%20Profissional%20(Beta)%20do%20Sistema%20de%20Gestão%20de%20Estoques."
                        target="_blank" class="btn btn-primary w-100 shadow-elegant">
                        Conversar no WhatsApp
                    </a>
                </article>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contato" class="footer">
        <div class="container">
            <div class="footer-grid flex justify-around">
                <div class="footer-brand">
                    <div class="brand">
                        <div class="">
                            <img src="{{ asset('imagens/icon.png') }}" width="100" alt="Logo"
                                class="brand-image" style="opacity:.8">
                        </div>
                        <span class="brand-title">Gestão de Estoques</span>
                    </div>
                    <p class="muted">
                        Solução completa para gestão inteligente de inventário, múltiplas lojas e controle de vendas.
                    </p>
                </div>

                <div>
                    <h4>Produto</h4>
                    <ul class="foot-list">
                        <li><a href="#recursos">Recursos</a></li>
                        <li><a href="#planos">Planos</a></li>
                        <li><a href="#demo">Demonstração</a></li>
                        <li><a href="#">Documentação</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Empresa</h4>
                    <ul class="foot-list">
                        <li><a href="#">Sobre</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#contato">Contato</a></li>
                        <li><a href="#">Suporte</a></li>
                    </ul>
                </div>
            </div>

            <div class="copy">
                <p>&copy; 2025 SyntaxWeb. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('navToggle');
            const links = document.getElementById('navLinks');

            toggle.addEventListener('click', () => {
                links.classList.toggle('open');
                toggle.classList.toggle('active');
            });
        });
    </script>
</body>

</html>
