# Levantamento do sistema Estoque

Este documento descreve os modulos e funcoes identificados no codigo do sistema, com uma estimativa de tempo de desenvolvimento por modulo.

## Base tecnica
- Backend: Laravel (routes/controllers/services/models).
- Frontend: Blade + Livewire.
- Autenticacao: Jetstream + Fortify + Sanctum.
- Tabelas: Rappasoft Laravel Livewire Tables.
- Relatorios: MPDF para PDF.

## Escopo analisado
- Rotas: `routes/web.php`.
- Controllers: `app/Http/Controllers`.
- Livewire: `app/Livewire`.
- Models: `app/Models`.
- Services/Middleware: `app/Services`, `app/Http/Middleware`.
- Views: `resources/views`.

## Premissas de estimativa
- Estimativa para 1 dev fullstack, 8h/dia.
- Inclui: backend, telas, validacoes, integracoes internas, ajustes visuais basicos.
- Nao inclui: infraestrutura/DevOps, migracao de dados, conteudo, treinamento, testes automatizados completos, homologacao formal.
- Valores em horas (h) e dias (d), sendo 1d = 8h.

## Modulos e funcoes

### 1) Autenticacao e controle de acesso
**Descricao**
- Login, logout, recuperacao de senha, verificacao de email, 2FA.
- Perfil de usuario e sessoes (Jetstream).
- Middleware de acesso por perfil (admin, gerente, operador, vendedor).

**Principais referencias**
- `app/Models/User.php`
- `app/Http/Middleware/VerificaPerfil.php`
- `app/Providers/FortifyServiceProvider.php`
- `app/Providers/JetstreamServiceProvider.php`
- `resources/views/auth`, `resources/views/profile`, `resources/views/teams`

**Estimativa**: 32h a 48h (4d a 6d)

### 2) Usuarios e perfis (administracao)
**Descricao**
- CRUD basico de usuarios (listar, criar, excluir).
- Atualizacao via Livewire (nome, email, senha, foto, perfil).

**Principais referencias**
- `app/Http/Controllers/UsuarioController.php`
- `app/Livewire/Usuario/AtualizarUsuario.php`
- `resources/views/configurar/usuario`

**Estimativa**: 24h a 32h (3d a 4d)

### 3) Empresa (dados corporativos)
**Descricao**
- Edicao de dados da empresa (nome, razao social, cnpj, contato).
- Upload e troca de logo (armazenamento local).

**Principais referencias**
- `app/Http/Controllers/EmpresaController.php`
- `app/Models/Empresa.php`
- `resources/views/empresa`

**Estimativa**: 12h a 16h (1.5d a 2d)

### 4) Lojas
**Descricao**
- CRUD de lojas vinculadas a empresa (nome, endereco, telefone).

**Principais referencias**
- `app/Http/Controllers/LojaController.php`
- `app/Models/Loja.php`
- `resources/views/lojas`

**Estimativa**: 12h a 16h (1.5d a 2d)

### 5) Estoques
**Descricao**
- CRUD de estoques e vinculacao a loja.
- Limite maximo de itens por estoque.
- Exclusao logica com restauracao e ajuste de movimentacoes.
- Auditoria de operacoes.

**Principais referencias**
- `app/Http/Controllers/EstoqueController.php`
- `app/Models/Estoque.php`
- `resources/views/estoque`, `resources/views/estoques`

**Estimativa**: 24h a 32h (3d a 4d)

### 6) Categorias
**Descricao**
- CRUD de categorias com status ativo/inativo.
- Bloqueio de exclusao se houver produtos vinculados.

**Principais referencias**
- `app/Http/Controllers/CategoriaController.php`
- `app/Models/Categoria.php`
- `resources/views/categorias`

**Estimativa**: 10h a 14h (1.25d a 1.75d)

### 7) Fabricantes
**Descricao**
- CRUD parcial de fabricantes (listar, criar, excluir).

**Principais referencias**
- `app/Http/Controllers/FabricanteController.php`
- `app/Models/Fabricante.php`
- `resources/views/fabricantes`

**Estimativa**: 8h a 12h (1d a 1.5d)

### 8) Produtos e unidades fisicas
**Descricao**
- Cadastro de produto com imagem, categoria, fabricante e estoque.
- Geracao de codigo de barras e unidades fisicas por quantidade.
- Atualizacao de produto (preco, estoque, imagem, categoria, fabricante).
- Exclusao logica/inativacao com registro de movimentacao.
- Controle de unidades (disponivel, vendido, reservado, defeito).
- Tabelas com filtros por estoque/status.

**Principais referencias**
- `app/Http/Controllers/ProdutosController.php`
- `app/Services/ProdutosService.php`
- `app/Services/ProdutoUnidadeService.php`
- `app/Services/MovimentacaoService.php`
- `app/Models/Produto.php`, `app/Models/ProdutosUnidades.php`
- `app/Livewire/Produto/*`
- `resources/views/produto`, `resources/views/livewire/produto`

**Estimativa**: 70h a 90h (9d a 11.25d)

### 9) Movimentacoes e historico
**Descricao**
- Registro de movimentacoes (entrada, disponivel, saida, cancelamento).
- Historico por produto com usuario e observacoes.

**Principais referencias**
- `app/Services/MovimentacaoService.php`
- `app/Livewire/ProdutoMovimentacoesTable.php`
- `app/Models/Movimentacao.php`
- `resources/views/livewire`

**Estimativa**: 12h a 16h (1.5d a 2d)

### 10) Clientes e enderecos
**Descricao**
- CRUD de clientes com busca por nome/email/documento.
- Endereco padrao vinculado ao cliente.
- Relacionamento com vendas.

**Principais referencias**
- `app/Http/Controllers/ClienteController.php`
- `app/Models/Cliente.php`, `app/Models/EnderecoCliente.php`
- `resources/views/clientes`

**Estimativa**: 24h a 32h (3d a 4d)

### 11) Vendas e carrinho
**Descricao**
- Catalogo de produtos com busca e adicao ao carrinho.
- Carrinho em sessao com ajuste de quantidades.
- Confirmacao da venda com criacao de itens e baixa de unidades.
- Desconto por combo (produtos vinculados).
- Emissao de nota em PDF.
- Listagem de vendas e edicao de protocolo.
- Bloqueio temporal do modulo de vendas por middleware.

**Principais referencias**
- `app/Livewire/Carrinho/ConfirmarVenda.php`
- `app/Livewire/Carrinho/CarrinhoNavBar.php`
- `app/Livewire/Produto/CatalogoProduto.php`
- `app/Livewire/Vendas/VendasTable.php`
- `app/Livewire/Vendas/AtualizarVenda.php`
- `app/Http/Controllers/VendaController.php`
- `app/Services/VendaService.php`
- `app/Http/Middleware/BlockOldSalesModule.php`
- `resources/views/vendas`, `resources/views/livewire`

**Estimativa**: 90h a 120h (11.25d a 15d)

### 12) Auditoria
**Descricao**
- Registro centralizado de eventos (acao, usuario, IP, user-agent).
- Tela de consulta com filtros.

**Principais referencias**
- `app/Services/AuditLogger.php`
- `app/Http/Controllers/AuditLogController.php`
- `app/Models/AuditLog.php`
- `resources/views/auditoria`

**Estimativa**: 12h a 16h (1.5d a 2d)

### 13) Base de UI e componentes
**Descricao**
- Layout principal, navegacao e componentes reutilizaveis.
- Tabelas Livewire, modais dinamicos e toasts.

**Principais referencias**
- `resources/views/layouts/app.blade.php`
- `app/Livewire/ModalDinamico.php`
- `app/Livewire/Components/Toast.php`
- `resources/views/components`

**Estimativa**: 24h a 32h (3d a 4d)

## Totais estimados
- **Total minimo**: 342h (42.75d)
- **Total maximo**: 460h (57.5d)

## Observacoes
- As estimativas podem variar conforme ajustes de escopo, integracoes externas, regras fiscais e layout.
- Se houver necessidade de testes automatizados, revisao UX/UI ou migracao de dados, o tempo deve ser revisto.
