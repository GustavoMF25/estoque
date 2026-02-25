# Gestao Estoque Lite

Versao minima do sistema focada em controle de estoque com ativacao por token.

## Escopo da versao Lite

Inclui:
- Configuracao de banco de dados no assistente de instalacao
- Setup automatico (`migrate` + `seed`)
- Cadastro inicial (empresa + primeiro usuario admin)
- Ativacao por token (licenca)
- Dashboard de estoque
- Estoques
- Produtos
- Estoque a chegar
- Cadastros de apoio: categorias, fabricantes, lojas
- Administracao basica: usuarios, empresa, auditoria

Nao inclui:
- Modulo comercial/vendas
- Carrinho
- Nota fiscal
- Clientes
- Notificacoes

## Requisitos

- PHP 8.2+
- Composer 2+
- MySQL 8+
- Extensoes PHP comuns do Laravel (pdo, mbstring, openssl, tokenizer, xml, ctype, json)

## Instalacao (cliente final)

1. Extrair o pacote em um servidor web.
2. Criar o arquivo `.env` a partir do `.env.example`.
3. Acessar `/instalacao`.
4. Seguir o wizard:
- Etapa 1: informar dados do banco
- Etapa 2: executar setup automatico
- Etapa 3: cadastrar empresa e primeiro usuario
- Etapa 4: ativar com token de licenca
5. Acessar `/login` e entrar com o usuario admin criado.

## Fluxo de ativacao

- O token de licenca e fornecido pelo vendedor.
- O token e gerado no sistema de gestao de licencas.
- O token deve ser usado em apenas 1 instalacao.
- A instalacao fica vinculada ao `installation_id`.
- O sistema faz validacoes periodicas da licenca.

## Operacao basica

- Cadastrar loja/estoque
- Cadastrar produtos
- Registrar estoque a chegar
- Administrar usuarios do cliente

## Suporte

Canal e prazo de suporte devem ser definidos na proposta comercial.
