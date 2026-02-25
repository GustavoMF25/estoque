# Checklist de Entrega - Gestao Estoque Lite

Use este checklist antes de entregar ao cliente.

## 1. Pacote

- [ ] Branch correta (`minimal-estoque`)
- [ ] Arquivos desnecessarios removidos
- [ ] `.env.example` sem credenciais reais
- [ ] ZIP gerado sem pasta `vendor` (ou incluir instrucao clara)
- [ ] Documentos incluidos: `README-LITE.md`, `TERMS-LITE.md`

## 2. Seguranca

- [ ] `APP_ENV=production` na orientacao de deploy
- [ ] `APP_DEBUG=false` na orientacao de deploy
- [ ] Chaves e tokens sensiveis fora do pacote
- [ ] Permissoes de `storage` e `bootstrap/cache` documentadas

## 3. Instalacao

- [ ] Tela `/instalacao` abre em ambiente limpo
- [ ] Etapa 1 (banco) valida conexao corretamente
- [ ] Etapa 2 executa `migrate` e `seed`
- [ ] Etapa 3 cria empresa e primeiro admin
- [ ] Etapa 4 ativa token com sucesso
- [ ] Login funciona apos ativacao

## 4. Licenciamento

- [ ] Token de teste criado no sistema de gestao
- [ ] Token marca como usado apos ativacao
- [ ] Reuso do mesmo token e bloqueado
- [ ] `installation_id` vinculado corretamente

## 5. Validacao funcional (Lite)

- [ ] Dashboard abre sem erro
- [ ] CRUD de estoques funcionando
- [ ] CRUD de produtos funcionando
- [ ] Estoque a chegar funcionando
- [ ] Cadastros de apoio (categorias/fabricantes/lojas) funcionando
- [ ] Usuarios (admin) funcionando

## 6. Comercial

- [ ] Descricao da oferta alinhada ao escopo Lite
- [ ] Limites da versao descritos sem ambiguidade
- [ ] Canal e prazo de suporte definidos
- [ ] Politica de atualizacao definida

## 7. Pos-venda

- [ ] Mensagem padrao de boas-vindas enviada
- [ ] Dados de acesso entregues em canal seguro
- [ ] Check de ativacao confirmado com cliente
- [ ] Ticket inicial de suporte (se contratado) aberto
