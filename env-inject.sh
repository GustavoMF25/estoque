#!/bin/bash

# CONFIGURAÇÕES DO SEU APP
CAPROVER_URL="http://localhost:3000"
CAPROVER_PASSWORD="12345678"
APP_NAME="estoque-syntax"

# 1. Obter token de autenticação
echo "Autenticando no CapRover..."
AUTH_TOKEN_RAW=$(curl -s -X POST "$CAPROVER_URL/api/v2/login" \
  -H "Content-Type: application/json" \
  -d "{\"password\":\"$CAPROVER_PASSWORD\"}")

# echo "$AUTH_TOKEN_RAW"

AUTH_TOKEN=$(echo "$AUTH_TOKEN_RAW" | jq -r '.data.token')

if [[ "$AUTH_TOKEN" == "null" || "$AUTH_TOKEN" == "" ]]; then
  echo "Erro: não foi possível autenticar no CapRover."
  exit 1
fi

# 2. Definir variáveis
echo "Enviando variáveis de ambiente para o app $APP_NAME..."

curl -s -X POST "$CAPROVER_URL/api/v2/user/apps/env-configs" \
  -H "Content-Type: application/json" \
  -H "captain-auth: $AUTH_TOKEN" \
  -d "{
  \"appName\": \"$APP_NAME\",
  \"envVars\": {
    \"APP_NAME\": \"Laravel\",
    \"APP_ENV\": \"local\",
    \"APP_DEBUG\": \"true\",
    \"APP_URL\": \"http://estoque-syntax.captain.localhost\",
    \"LOG_CHANNEL\": \"stack\",
    \"DB_CONNECTION\": \"mysql\",
    \"DB_HOST\": \"srv-captain--mysql-db\",
    \"DB_PORT\": \"3306\",
    \"DB_DATABASE\": \"estoque\",
    \"DB_USERNAME\": \"estoque_user\",
    \"DB_PASSWORD\": \"secret\",
    \"CACHE_DRIVER\": \"file\",
    \"QUEUE_CONNECTION\": \"sync\",
    \"SESSION_DRIVER\": \"file\"
  }
}" | jq .

echo "✅ Variáveis enviadas com sucesso!"
