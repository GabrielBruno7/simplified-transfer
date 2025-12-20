# Simplified Transfer

Um sistema simples de transferências financeiras construído com Laravel. O projeto simula funcionalidades básicas de uma carteira digital, permitindo que usuários façam login, se cadastrem e realizem transferências.

## Funcionalidades

- Autenticação de usuários com JWT
- Cadastro de novos usuários
- Sistema de transferências entre carteiras
- Validação e autorização de transferências
- Notificações por email
- Sistema de logs para monitoramento e debug
- Extrato de transferências por usuário

## API Endpoints

### Rotas Públicas

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/api/login` | Realiza login do usuário |
| `POST` | `/api/user` | Cadastra um novo usuário |

### Rotas Protegidas (JWT)

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `POST` | `/api/transfer` | Realiza uma transferência entre usuários |
| `GET` | `/api/user/{document}/statement` | Obtém o extrato de transferências do usuário |

## Como usar

1. Faça login ou cadastre um novo usuário
2. Use o token JWT retornado no login para acessar rotas protegidas
3. Realize transferências informando o destinatário e valor
4. Consulte o extrato de transferências usando o documento do usuário

## Sistema de Logs

O projeto implementa um sistema robusto de logs que registra erros e eventos importantes:

- **Logs de aplicação**: Armazenados no banco de dados na tabela `logs`
- **Níveis de log**: Error e Warning baseado no tipo de exceção
- **Estrutura**: Cada log contém ID, nível, mensagem, exceção, contexto, ambiente e timestamp
- **Tratamento de exceções**: Diferencia entre exceções de usuário e erros internos

### Estrutura dos Logs

```json
{
  "id": "uuid",
  "level": "error|warning",
  "message": "Descrição do erro",
  "exception": "Nome da classe da exceção",
  "context": { /* dados adicionais */ },
  "environment": "local|production",
  "created_at": "2025-12-20T10:30:00.000Z"
}
```

## Docker

O projeto está configurado para execução em containers Docker com os seguintes serviços:

### Serviços Disponíveis

- **App**: Aplicação PHP 8.2 com FPM
- **Nginx**: Servidor web (porta 8001)
- **MySQL**: Banco de dados (porta 3307)
- **phpMyAdmin**: Interface web para banco (porta 8082)

### Comandos Docker

```bash
# Subir todos os serviços
docker-compose up -d

# Parar todos os serviços
docker-compose down

# Executar comandos dentro do container da aplicação
docker-compose exec app php artisan migrate
docker-compose exec app composer install
```

### Acessos

- **Aplicação**: http://localhost:8001
- **phpMyAdmin**: http://localhost:8082 (admin/admin)
- **MySQL**: localhost:3307 (admin/admin)

## Estrutura do Projeto

O projeto segue uma arquitetura em camadas com Domain-Driven Design (DDD):

- `/domain` - Regras de negócio e entidades do domínio
- `/infra` - Implementações de infraestrutura (banco, email, etc.)
- `/app` - Controllers e middlewares do Laravel
