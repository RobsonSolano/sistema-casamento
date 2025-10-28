# 📋 Resumo da Estrutura do Projeto SaaS - MeuCasamento.com.br

**Versão:** 2.0 (Completa com Auth Separado)  
**Data:** 28/10/2025  
**Status:** Planejamento/Documentação

---

## 🎯 O Que Foi Adicionado (v2.0)

### ✅ Novas Tabelas de Autenticação e Segurança

1. **`users`** - Reformulado
   - Agora são **apenas clientes** (noivos)
   - Roles: `owner` (dono), `collaborator` (ajudante)
   - `tenant_id` é obrigatório (não pode ser NULL)

2. **`user_profiles`** - NOVO
   - Perfil completo do usuário
   - CPF para nota fiscal
   - Endereço completo
   - Redes sociais (Instagram, Facebook, WhatsApp)
   - Preferências JSON (notificações, idioma)

3. **`admin_users`** - NOVO
   - Tabela **separada** para gestores da plataforma
   - Roles: `super_admin`, `support`, `financial`, `developer`
   - Permissões granulares em JSON
   - Hierarquia (admin que criou outro admin)

4. **`sessions`** - NOVO
   - Controle de sessões JWT
   - Suporta **cliente OU admin** (constraint)
   - Refresh token
   - Rastreia device type, IP, user agent
   - Expiração e inatividade

5. **`password_resets`** - NOVO
   - Tokens de recuperação de senha
   - Para clientes E admins
   - Expira após uso ou timeout
   - Rastreamento de IP

6. **`login_attempts`** - NOVO
   - Proteção contra brute force
   - Log de tentativas (sucesso/falha)
   - IP + Email para rate limiting
   - Análise de segurança

---

## 📊 Total de Tabelas: 18

| # | Tabela | Categoria | Descrição |
|---|--------|-----------|-----------|
| 1 | `tenants` | Core | Multi-tenancy (organizações) |
| 2 | `users` | Auth | Clientes (noivos) |
| 3 | `user_profiles` | Auth | Perfil detalhado |
| 4 | `admin_users` | Auth | Gestores plataforma |
| 5 | `sessions` | Auth | Controle JWT |
| 6 | `password_resets` | Auth | Recuperação senha |
| 7 | `login_attempts` | Segurança | Anti brute force |
| 8 | `templates` | Core | Layouts disponíveis |
| 9 | `weddings` | Core | Sites casamento |
| 10 | `subscriptions` | Receita | Pagamentos |
| 11 | `presentes` | Feature | Lista presentes |
| 12 | `pix_transactions` | Feature | Transações PIX |
| 13 | `recados` | Feature | Mensagens convidados |
| 14 | `gallery_images` | Feature | Fotos galeria |
| 15 | `rsvp` | Feature | Confirmação presença |
| 16 | `activity_logs` | Auditoria | Log de ações |
| 17 | `discount_codes` | Marketing | Cupons desconto |
| 18 | `email_queue` | Infra | Fila emails |

---

## 🔐 Separação de Auth: Cliente vs Admin

### Fluxo de Login Cliente
```
POST /api/auth/login
{
  "email": "maria@email.com",
  "password": "***"
}

↓

1. Valida em `users` (WHERE tenant_id IS NOT NULL)
2. Verifica senha (password_hash)
3. Registra em `login_attempts` (success=true)
4. Cria sessão em `sessions` (user_id preenchido)
5. Gera JWT com payload:
   {
     "user_id": 123,
     "tenant_id": 45,
     "role": "owner",
     "type": "client"
   }
6. Retorna token + refresh_token
```

### Fluxo de Login Admin
```
POST /api/admin/auth/login
{
  "email": "admin@meucasamento.com.br",
  "password": "***"
}

↓

1. Valida em `admin_users`
2. Verifica senha
3. Opcional: 2FA (se configurado)
4. Registra em `login_attempts`
5. Cria sessão em `sessions` (admin_user_id preenchido)
6. Gera JWT com payload:
   {
     "admin_user_id": 1,
     "role": "super_admin",
     "type": "admin",
     "permissions": {...}
   }
7. Retorna token + refresh_token
```

### Middleware de Autenticação

```php
class AuthMiddleware {
    public function handle($request) {
        $token = $request->bearerToken();
        
        // Valida JWT
        $payload = JWT::decode($token, $secret);
        
        // Verifica se sessão existe e está ativa
        if ($payload->type === 'client') {
            $session = Session::where('token', $token)
                             ->where('user_id', $payload->user_id)
                             ->where('is_active', true)
                             ->where('expires_at', '>', now())
                             ->first();
            
            if (!$session) {
                throw new UnauthorizedException();
            }
            
            // Atualiza last_activity
            $session->touch();
            
            // Injeta user no request
            $request->user = User::find($payload->user_id);
            $request->tenant_id = $payload->tenant_id;
            
        } else if ($payload->type === 'admin') {
            // Similar para admin...
        }
    }
}
```

---

## 🗂️ Arquivos Criados

### 📄 Documentação Texto
- `produto-planejamento.md` - Visão de negócio, roadmap, receita
- `estrutura-saas.sql` - Schema completo das 18 tabelas
- `diagrama-der.md` - DER textual com explicações
- `README-DIAGRAMAS.md` - Como visualizar/exportar

### 🖼️ Diagramas Mermaid (Fonte)
- `diagrama-er.mmd` - DER completo (todas tabelas)
- `diagrama-er-simples.mmd` - DER simplificado
- `arquitetura-sistema.mmd` - Arquitetura completa
- `arquitetura-simples.mmd` - Arquitetura simplificada

### 📸 Imagens Geradas (JPG)
- `diagrama-er.jpg` - DER visual (37 KB)
- `arquitetura-sistema.jpg` - Arquitetura visual (29 KB)

### 🛠️ Utilitários
- `gerar-diagramas.py` - Script Python para gerar imagens

---

## 🔄 Relacionamentos Principais

```
TENANT (1) ─────< (N) USERS
                    │
                    └──── (1:1) USER_PROFILES

TENANT (1) ─────< (N) WEDDINGS
                    │
                    ├──< (N) PRESENTES ───< (N) PIX_TRANSACTIONS
                    ├──< (N) RECADOS
                    ├──< (N) GALLERY_IMAGES
                    └──< (N) RSVP

TENANT (1) ─────< (N) SUBSCRIPTIONS

ADMIN_USERS (1) ─< (N) ADMIN_USERS (hierarquia)
            │
            ├──< (N) SESSIONS
            ├──< (N) PASSWORD_RESETS
            └──< (N) DISCOUNT_CODES

USERS/ADMIN_USERS ───< (N) SESSIONS
                  │
                  └──< (N) PASSWORD_RESETS
```

---

## 🚀 Próximos Passos

### 1. Implementação do Auth
```bash
# Criar classes/interfaces:
- AuthController (login, register, refresh, logout)
- AdminAuthController (login admin separado)
- AuthMiddleware (validação JWT)
- SessionManager (criar/invalidar sessões)
- PasswordResetService (enviar email, validar token)
- LoginAttemptService (rate limiting)
```

### 2. Migração do Banco Atual
```sql
-- Backup do banco atual
mysqldump pessoal_casamento_mari_douglas > backup_old.sql

-- Criar novo banco
CREATE DATABASE pessoal_casamento_saas;

-- Importar estrutura nova
mysql pessoal_casamento_saas < estrutura-saas.sql

-- Migrar dados antigos (script customizado)
```

### 3. Refatorar Código Existente
- Adicionar `tenant_id` em todas queries
- Criar TenantResolver (identifica por subdomain)
- Implementar RBAC (Role-Based Access Control)
- Adicionar validação de assinatura ativa

### 4. Testes
- Isolamento de dados entre tenants
- Expiração de tokens
- Rate limiting de login
- Recovery de senha

---

## 💡 Decisões Arquiteturais

### Por que separar `users` e `admin_users`?

✅ **Vantagens:**
- Segurança: Ataques em cliente não afetam admin
- Clareza: Código fica mais legível
- Flexibilidade: Permissões diferentes sem conflito
- Compliance: Logs separados para auditoria

❌ **Alternativa descartada:** 
- Uma única tabela `users` com `tenant_id NULLABLE`
- Problema: Complexidade nas queries, risco de vazamento de dados

### Por que tabela `sessions` ao invés de apenas JWT?

✅ **Vantagens:**
- Revogação instantânea (logout, ban)
- Múltiplos devices (listar, remover)
- Analytics (devices, IPs, horários)
- Refresh token seguro

❌ **Alternativa descartada:**
- JWT stateless puro
- Problema: Não tem como invalidar antes de expirar

### Por que `login_attempts` separado?

✅ **Vantagens:**
- Rate limiting eficiente
- Análise de ataques
- Não polui tabela de usuários
- Rotação de dados (delete old > 90 days)

---

## 📈 Métricas Esperadas

### Performance
- Login: < 200ms
- Lista presentes (100 items): < 300ms
- Upload imagem galeria: < 2s

### Segurança
- Rate limit login: 5 tentativas / 15min
- Expiração token: 24h (refresh 30d)
- Sessão inativa: logout 7 dias

### Escalabilidade
- Suporta: 10.000 tenants simultâneos
- Cache hit ratio: > 80%
- Queries com índices: 100%

---

## 📞 Contato

**Projeto:** MeuCasamento.com.br SaaS  
**Desenvolvedor:** Robson  
**Repositório:** (privado)

---

**Última atualização:** 28/10/2025 às 10:42 BRT

