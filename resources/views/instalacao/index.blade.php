<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalação do Sistema</title>
    <link href="{{ asset('adminlte/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --bg: #e9eef3;
            --card: #ffffff;
            --header-a: #061c24;
            --header-b: #0d5b43;
            --primary: #0f6a55;
            --primary-dark: #0b5544;
            --text: #1f2f3a;
            --muted: #748695;
            --border: #d9e2ea;
            --soft: #eef4fa;
            --ok-bg: #e8f5ee;
            --ok-text: #1b7a49;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 20% 10%, #ffffff 0%, transparent 35%), var(--bg);
            padding: 20px;
            color: var(--text);
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
        }

        .wizard-card {
            width: 100%;
            max-width: 840px;
            background: var(--card);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 20px 46px rgba(15, 23, 42, .11);
        }

        .card-header {
            background: linear-gradient(120deg, var(--header-a), var(--header-b));
            padding: 18px 22px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-header img {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            padding: 6px;
            background: #fff;
            object-fit: contain;
        }

        .card-header h1 {
            margin: 0;
            font-size: 1.55rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .card-header p {
            margin: 5px 0 0;
            font-size: .98rem;
            opacity: .9;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding: 12px 16px;
            background: #f5f9fc;
            border-bottom: 1px solid var(--border);
        }

        .step {
            text-align: center;
            font-size: .83rem;
            font-weight: 700;
            padding: 10px 8px;
            border-radius: 999px;
            background: #e5ecf3;
            color: #627483;
            transition: .2s ease;
        }

        .step.active {
            background: #dcefe7;
            color: #176f43;
        }

        .step.done {
            background: #e7f6ee;
            color: #176f43;
        }

        .card-body {
            padding: 22px;
        }

        .title {
            margin: 0 0 4px;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: .1px;
            color: #17314a;
        }

        .info {
            background: var(--soft);
            border: 1px solid #d8e4ef;
            color: #3f5566;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 1.15rem;
            margin-bottom: 18px;
        }

        .form-layout {
            display: grid;
            grid-template-columns: 1.25fr .75fr;
            gap: 18px;
        }

        .form-section {
            background: #fbfdff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
        }

        .form-group {
            margin-bottom: 11px;
        }

        .form-label {
            display: block;
            font-size: .95rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: #2a3d4a;
        }

        .form-control {
            height: 44px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 1rem;
            background: #fff;
        }

        textarea.form-control {
            height: auto;
            min-height: 84px;
        }

        .form-control:focus {
            border-color: #8dbda9;
            box-shadow: 0 0 0 .2rem rgba(15, 106, 85, .12);
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        .aside {
            background: #f8fbfd;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            font-size: .95rem;
            color: #586b79;
        }

        .aside h4 {
            margin: 0 0 8px;
            font-size: 1rem;
            font-weight: 700;
            color: #314757;
        }

        .status-ok {
            margin-top: 10px;
            background: var(--ok-bg);
            border: 1px solid #cde8d8;
            color: var(--ok-text);
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 700;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
        }

        .btn-ui {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 46px;
            min-width: 172px;
            border-radius: 11px;
            padding: 0 18px;
            font-size: 1.03rem;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary-ui {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary-ui:hover {
            background: var(--primary-dark);
            color: #fff;
            text-decoration: none;
        }

        .btn-light-ui {
            background: #fff;
            border-color: var(--border);
            color: #334958;
        }

        .btn-light-ui:hover {
            background: #f4f8fb;
            text-decoration: none;
            color: #2b3f4c;
        }

        .btn-next-ui {
            background: #eef8f2;
            border-color: #cfead8;
            color: #1d7749;
        }

        .btn-next-ui:hover {
            background: #e3f4eb;
            color: #17663f;
            text-decoration: none;
        }

        .footer-note {
            margin-top: 14px;
            text-align: center;
            color: #7f8d9b;
            font-size: .9rem;
        }

        @media (max-width: 900px) {
            .form-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .steps {
                grid-template-columns: repeat(2, 1fr);
            }

            .title {
                font-size: 1.6rem;
            }

            .info {
                font-size: 1rem;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-ui {
                width: 100%;
                min-width: 0;
            }
        }
        .text-danger{
            color: #c82333;
        }
    </style>
</head>
<body>
    @php
        $stepAtual = $stepAtual ?? 1;
        $status = [
            1 => $bancoConfigurado ?? false,
            2 => $setupConcluido ?? false,
            3 => $cadastroConcluido ?? false,
            4 => optional($config)->is_installed ?? false,
        ];
    @endphp

    <section class="wizard-card">
        <header class="card-header">
            <img src="{{ asset('imagens/icon.png') }}" alt="Ícone do sistema">
            <div>
                <h1>Ativação da Licença</h1>
                <p>Conclua a instalação com seu token de acesso</p>
            </div>
        </header>

        <div class="steps">
            <div class="step {{ $stepAtual === 1 ? 'active' : '' }} {{ $status[1] ? 'done' : '' }}">1. Banco</div>
            <div class="step {{ $stepAtual === 2 ? 'active' : '' }} {{ $status[2] ? 'done' : '' }}">2. Setup</div>
            <div class="step {{ $stepAtual === 3 ? 'active' : '' }} {{ $status[3] ? 'done' : '' }}">3. Cadastro</div>
            <div class="step {{ $stepAtual === 4 ? 'active' : '' }} {{ $status[4] ? 'done' : '' }}">4. Licença</div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert info alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger info text-danger mb-3">{{ $errors->first() }}</div>
            @endif

            @if ($stepAtual === 1)
                <h2 class="title">Configuração do Banco</h2>
                <div class="info">Informe os dados do banco para liberar o setup automático.</div>
                <form method="POST" action="{{ route('instalacao.banco') }}">
                    @csrf
                    <div class="form-layout">
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-4 form-group"><label class="form-label">Conexão</label><input type="text" name="db_connection" class="form-control" value="{{ old('db_connection', $dbConfig['db_connection'] ?? 'mysql') }}" required></div>
                                <div class="col-md-8 form-group"><label class="form-label">Host</label><input type="text" name="db_host" class="form-control" value="{{ old('db_host', $dbConfig['db_host'] ?? '') }}" required></div>
                                <div class="col-md-4 form-group"><label class="form-label">Porta</label><input type="number" name="db_port" class="form-control" value="{{ old('db_port', $dbConfig['db_port'] ?? '3306') }}" required></div>
                                <div class="col-md-8 form-group"><label class="form-label">Database</label><input type="text" name="db_database" class="form-control" value="{{ old('db_database', $dbConfig['db_database'] ?? '') }}" required></div>
                                <div class="col-md-6 form-group"><label class="form-label">Usuário</label><input type="text" name="db_username" class="form-control" value="{{ old('db_username', $dbConfig['db_username'] ?? '') }}" required></div>
                                <div class="col-md-6 form-group"><label class="form-label">Senha</label><input type="password" name="db_password" class="form-control" value="{{ old('db_password', $dbConfig['db_password'] ?? '') }}"></div>
                            </div>
                        </div>
                        <aside class="aside">
                            <h4>Resumo desta etapa</h4>
                            <p>O sistema testa a conexão e salva os dados no <strong>.env</strong>.</p>
                            <p>Sem esta validação, os próximos passos ficam bloqueados.</p>
                        </aside>
                    </div>
                    <div class="actions">
                        <span></span>
                        <button type="submit" class="btn-ui btn-primary-ui">Salvar e Testar Conexão</button>
                    </div>
                </form>

                @if ($bancoConfigurado)
                    <div class="status-ok">Conexão validada com sucesso.</div>
                    <div class="actions"><span></span><a href="{{ route('instalacao.index', ['step' => 2]) }}" class="btn-ui btn-next-ui">Próximo passo</a></div>
                @endif
            @endif

            @if ($stepAtual === 2)
                <h2 class="title">Setup Automático</h2>
                <div class="info">Executa migrations e seeders básicos da instalação.</div>
                <div class="form-layout">
                    <div class="form-section">
                        <form method="POST" action="{{ route('instalacao.setup') }}">
                            @csrf
                            <button type="submit" class="btn-ui btn-primary-ui" {{ $bancoConfigurado ? '' : 'disabled' }} {{ $setupConcluido ? 'disabled' : '' }}>Executar Setup</button>
                        </form>
                    </div>
                    <aside class="aside">
                        <h4>O que acontece aqui?</h4>
                        <p>As tabelas são criadas e os dados iniciais são inseridos automaticamente.</p>
                    </aside>
                </div>
                <div class="actions">
                    <a href="{{ route('instalacao.index', ['step' => 1]) }}" class="btn-ui btn-light-ui">Voltar</a>
                    @if ($setupConcluido)
                        <a href="{{ route('instalacao.index', ['step' => 3]) }}" class="btn-ui btn-next-ui">Próximo passo</a>
                    @endif
                </div>
            @endif

            @if ($stepAtual === 3)
                <h2 class="title">Cadastro Inicial</h2>
                <div class="info">Cadastre a empresa e o primeiro usuário administrador.</div>
                <form method="POST" action="{{ route('instalacao.cadastro-inicial') }}">
                    @csrf
                    <div class="form-layout">
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-6 form-group"><label class="form-label">Nome da empresa</label><input type="text" name="empresa_nome" class="form-control" value="{{ old('empresa_nome') }}" required></div>
                                <div class="col-md-6 form-group"><label class="form-label">Razão social</label><input type="text" name="empresa_razao_social" class="form-control" value="{{ old('empresa_razao_social') }}"></div>
                                <div class="col-md-4 form-group"><label class="form-label">CNPJ</label><input type="text" name="empresa_cnpj" class="form-control" value="{{ old('empresa_cnpj') }}" required></div>
                                <div class="col-md-4 form-group"><label class="form-label">Telefone</label><input type="text" name="empresa_telefone" class="form-control" value="{{ old('empresa_telefone') }}"></div>
                                <div class="col-md-4 form-group"><label class="form-label">E-mail da empresa</label><input type="email" name="empresa_email" class="form-control" value="{{ old('empresa_email') }}"></div>
                                <div class="col-md-12 form-group"><label class="form-label">Endereço</label><textarea name="empresa_endereco" class="form-control">{{ old('empresa_endereco') }}</textarea></div>
                                <div class="col-md-6 form-group"><label class="form-label">Nome do administrador</label><input type="text" name="usuario_nome" class="form-control" value="{{ old('usuario_nome') }}" required></div>
                                <div class="col-md-6 form-group"><label class="form-label">E-mail do administrador</label><input type="email" name="usuario_email" class="form-control" value="{{ old('usuario_email') }}" required></div>
                                <div class="col-md-6 form-group"><label class="form-label">Senha</label><input type="password" name="usuario_password" class="form-control" required></div>
                                <div class="col-md-6 form-group"><label class="form-label">Confirmar senha</label><input type="password" name="usuario_password_confirmation" class="form-control" required></div>
                            </div>
                        </div>
                        <aside class="aside">
                            <h4>Dica</h4>
                            <p>Use um e-mail válido para o administrador principal.</p>
                            <p>Este usuário terá controle total do sistema.</p>
                        </aside>
                    </div>
                    <div class="actions"><a href="{{ route('instalacao.index', ['step' => 2]) }}" class="btn-ui btn-light-ui">Voltar</a><button type="submit" class="btn-ui btn-primary-ui">Salvar Cadastro</button></div>
                </form>
                @if ($cadastroConcluido)
                    <div class="actions"><span></span><a href="{{ route('instalacao.index', ['step' => 4]) }}" class="btn-ui btn-next-ui">Próximo passo</a></div>
                @endif
            @endif

            @if ($stepAtual === 4)
                <h2 class="title">Ativação da Licença</h2>
                <div class="info">Informe o token recebido para liberar esta instalação.</div>
                <form method="POST" action="{{ route('instalacao.ativar') }}">
                    @csrf
                    <div class="form-section">
                        <div class="form-group"><label class="form-label">ID da instalação</label><input type="text" class="form-control mono" value="{{ $instalacaoId }}" readonly></div>
                        <div class="form-group"><label class="form-label">Token da licença</label><input type="text" name="token" class="form-control" value="{{ old('token') }}" placeholder="GST-XXXX-XXXX-XXXX" required></div>
                    </div>
                    <div class="actions"><a href="{{ route('instalacao.index', ['step' => 3]) }}" class="btn-ui btn-light-ui">Voltar</a><button type="submit" class="btn-ui btn-primary-ui">Ativar Sistema</button></div>
                </form>
                <div class="footer-note">Após ativação, este ambiente ficará vinculado ao token utilizado.</div>
            @endif
        </div>
    </section>
</body>
</html>
