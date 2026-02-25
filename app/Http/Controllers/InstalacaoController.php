<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use App\Services\InstalacaoLicencaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InstalacaoController extends Controller
{
    public function __construct(private InstalacaoLicencaService $instalacaoLicencaService)
    {
    }

    public function index(): View|RedirectResponse
    {
        $dbConfig = $this->dbConfigAtual();
        $bancoConfigurado = $this->testarConexaoBanco($dbConfig);
        $stepSolicitado = max(1, min(4, (int) request()->integer('step', 1)));

        if (!$bancoConfigurado) {
            return view('instalacao.index', [
                'config' => null,
                'bancoConfigurado' => false,
                'setupConcluido' => false,
                'cadastroConcluido' => false,
                'instalacaoId' => 'Será gerado após o setup',
                'dbConfig' => $dbConfig,
                'stepAtual' => 1,
            ]);
        }

        if (!$this->temTabelaInstalacaoConfig()) {
            return view('instalacao.index', [
                'config' => null,
                'bancoConfigurado' => true,
                'setupConcluido' => false,
                'cadastroConcluido' => false,
                'instalacaoId' => 'Será gerado após o setup',
                'dbConfig' => $dbConfig,
                'stepAtual' => 2,
            ]);
        }

        $config = $this->instalacaoLicencaService->getOrCreateConfig();
        if ($config->is_installed) {
            return redirect()->route('dashboard');
        }

        $setupConcluido = !empty($config->setup_completed_at);
        $cadastroConcluido = !empty($config->onboarding_completed_at);
        $stepLiberado = $this->descobrirStepAtual($bancoConfigurado, $setupConcluido, $cadastroConcluido);
        $stepAtual = min($stepSolicitado, $stepLiberado);

        return view('instalacao.index', [
            'config' => $config,
            'bancoConfigurado' => true,
            'setupConcluido' => $setupConcluido,
            'cadastroConcluido' => $cadastroConcluido,
            'instalacaoId' => $config->installation_id,
            'dbConfig' => $dbConfig,
            'stepAtual' => $stepAtual,
        ]);
    }

    public function salvarBanco(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'db_connection' => ['required', 'string', 'max:20'],
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string', 'max:255'],
        ]);

        if (!$this->testarConexaoBanco($dados)) {
            return back()
                ->withErrors(['db' => 'Não foi possível conectar ao banco com os dados informados.'])
                ->withInput();
        }

        $this->atualizarEnv([
            'DB_CONNECTION' => $dados['db_connection'],
            'DB_HOST' => $dados['db_host'],
            'DB_PORT' => (string) $dados['db_port'],
            'DB_DATABASE' => $dados['db_database'],
            'DB_USERNAME' => $dados['db_username'],
            'DB_PASSWORD' => (string) ($dados['db_password'] ?? ''),
        ]);

        Artisan::call('config:clear');

        return redirect()->route('instalacao.index', ['step' => 2])
            ->with('success', 'Banco configurado e conexão validada com sucesso.');
    }

    public function executarSetup(): RedirectResponse
    {
        if (!$this->testarConexaoBanco($this->dbConfigAtual())) {
            return back()->withErrors(['setup' => 'Configure e valide o banco antes de executar o setup.']);
        }

        if ($this->temTabelaInstalacaoConfig()) {
            $config = $this->instalacaoLicencaService->getOrCreateConfig();
            if (!empty($config->setup_completed_at)) {
                return redirect()->route('instalacao.index', ['step' => 3])->with('success', 'Etapa de setup já foi concluída.');
            }
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } catch (\Throwable $e) {
            return redirect()->route('instalacao.index', ['step' => 2])->withErrors(['setup' => 'Falha ao executar migrate/seed: ' . $e->getMessage()]);
        }

        $config = $this->instalacaoLicencaService->getOrCreateConfig();
        $config->update([
            'setup_completed_at' => now(),
        ]);

        return redirect()->route('instalacao.index', ['step' => 3])->with('success', 'Setup concluído: migrations e seed executados.');
    }

    public function salvarCadastroInicial(Request $request): RedirectResponse
    {
        if (!$this->temTabelaInstalacaoConfig()) {
            return redirect()->route('instalacao.index', ['step' => 2])->withErrors(['setup' => 'Execute o setup automático antes do cadastro inicial.']);
        }

        $config = $this->instalacaoLicencaService->getOrCreateConfig();
        if (empty($config->setup_completed_at)) {
            return redirect()->route('instalacao.index', ['step' => 2])->withErrors(['setup' => 'Execute o setup automático antes do cadastro inicial.']);
        }

        $empresaAtual = Empresa::query()->first();
        $usuarioAtual = User::query()->where('perfil', 'admin')->first() ?: User::query()->first();

        $dados = $request->validate([
            'empresa_nome' => ['required', 'string', 'max:255'],
            'empresa_razao_social' => ['nullable', 'string', 'max:255'],
            'empresa_cnpj' => [
                'required',
                'string',
                'max:20',
                Rule::unique('empresas', 'cnpj')->ignore($empresaAtual?->id),
            ],
            'empresa_telefone' => ['nullable', 'string', 'max:20'],
            'empresa_email' => ['nullable', 'email', 'max:255'],
            'empresa_endereco' => ['nullable', 'string'],
            'usuario_nome' => ['required', 'string', 'max:255'],
            'usuario_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuarioAtual?->id),
            ],
            'usuario_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $empresa = $empresaAtual ?: new Empresa();
        $empresa->fill([
            'nome' => $dados['empresa_nome'],
            'razao_social' => $dados['empresa_razao_social'] ?? null,
            'cnpj' => $dados['empresa_cnpj'],
            'telefone' => $dados['empresa_telefone'] ?? null,
            'email' => $dados['empresa_email'] ?? null,
            'endereco' => $dados['empresa_endereco'] ?? null,
        ]);
        $empresa->save();

        $usuario = $usuarioAtual ?: new User();
        $usuario->fill([
            'name' => $dados['usuario_nome'],
            'email' => $dados['usuario_email'],
            'status' => 'ativo',
            'perfil' => 'admin',
        ]);
        $usuario->password = Hash::make($dados['usuario_password']);
        $usuario->email_verified_at = now();
        $usuario->save();

        $config->update([
            'onboarding_completed_at' => now(),
        ]);

        return redirect()->route('instalacao.index', ['step' => 4])->with('success', 'Empresa e primeiro usuário cadastrados com sucesso.');
    }

    public function ativar(Request $request): RedirectResponse
    {
        if (!$this->temTabelaInstalacaoConfig()) {
            return back()->withErrors(['token' => 'Execute o setup e cadastro inicial antes da ativação.']);
        }

        $config = $this->instalacaoLicencaService->getOrCreateConfig();
        if (empty($config->setup_completed_at) || empty($config->onboarding_completed_at)) {
            return back()->withErrors(['token' => 'Conclua setup e cadastro inicial antes de ativar.']);
        }

        $dados = $request->validate([
            'token' => ['required', 'string', 'min:10'],
        ]);

        $resultado = $this->instalacaoLicencaService->ativarToken($dados['token']);
        if (!$resultado['ok']) {
            return back()->withErrors(['token' => $resultado['message']])->withInput();
        }

        return redirect()->route('login')->with('success', 'Sistema ativado com sucesso.');
    }

    private function descobrirStepAtual(bool $bancoConfigurado, bool $setupConcluido, bool $cadastroConcluido): int
    {
        if (!$bancoConfigurado) {
            return 1;
        }

        if (!$setupConcluido) {
            return 2;
        }

        if (!$cadastroConcluido) {
            return 3;
        }

        return 4;
    }

    private function temTabelaInstalacaoConfig(): bool
    {
        try {
            return Schema::hasTable('instalacao_configs');
        } catch (\Throwable) {
            return false;
        }
    }

    private function dbConfigAtual(): array
    {
        return [
            'db_connection' => (string) env('DB_CONNECTION', 'mysql'),
            'db_host' => (string) env('DB_HOST', '127.0.0.1'),
            'db_port' => (string) env('DB_PORT', '3306'),
            'db_database' => (string) env('DB_DATABASE', ''),
            'db_username' => (string) env('DB_USERNAME', ''),
            'db_password' => (string) env('DB_PASSWORD', ''),
        ];
    }

    private function testarConexaoBanco(array $dados): bool
    {
        try {
            $driver = $dados['db_connection'] ?? 'mysql';
            if ($driver !== 'mysql') {
                return false;
            }

            $host = $dados['db_host'] ?? '';
            $port = (string) ($dados['db_port'] ?? '3306');
            $database = $dados['db_database'] ?? '';
            $username = $dados['db_username'] ?? '';
            $password = $dados['db_password'] ?? '';

            if (!$host || !$database || !$username) {
                return false;
            }

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);

            $pdo->query('SELECT 1');
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function atualizarEnv(array $values): void
    {
        $envPath = base_path('.env');
        $conteudo = file_exists($envPath) ? (string) file_get_contents($envPath) : '';

        foreach ($values as $chave => $valor) {
            $valorString = $this->formatarValorEnv((string) $valor);
            $pattern = '/^' . preg_quote($chave, '/') . '=.*$/m';

            if (preg_match($pattern, $conteudo)) {
                $conteudo = preg_replace($pattern, $chave . '=' . $valorString, $conteudo) ?? $conteudo;
            } else {
                $conteudo .= (Str::endsWith($conteudo, "\n") ? '' : "\n") . $chave . '=' . $valorString . "\n";
            }
        }

        file_put_contents($envPath, $conteudo);
    }

    private function formatarValorEnv(string $valor): string
    {
        if ($valor === '') {
            return '""';
        }

        if (preg_match('/[\\s#"\\\']/',$valor)) {
            return '"' . addslashes($valor) . '"';
        }

        return $valor;
    }
}
