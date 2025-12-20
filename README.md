# Simplified Transfer

Um sistema simples de transferências financeiras construído com Laravel. O projeto simula funcionalidades básicas de uma carteira digital, permitindo que usuários façam login, se cadastrem e realizem transferências.

## Funcionalidades

- Autenticação de usuários com JWT
- Cadastro de novos usuários
- Sistema de transferências entre carteiras
- Validação e autorização de transferências
- Notificações por email

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

## Como usar

1. Faça login ou cadastre um novo usuário
2. Use o token JWT retornado no login para acessar rotas protegidas
3. Realize transferências informando o destinatário e valor

## Estrutura do Projeto

O projeto segue uma arquitetura em camadas com Domain-Driven Design (DDD):

- `/domain` - Regras de negócio e entidades do domínio
- `/infra` - Implementações de infraestrutura (banco, email, etc.)
- `/app` - Controllers e middlewares do Laravel
