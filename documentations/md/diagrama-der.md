# Diagrama Entidade-Relacionamento (DER)
## MeuCasamento.com.br SaaS

---

## 📐 Diagrama Visual (ASCII)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          ARQUITETURA MULTI-TENANT                            │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌──────────────┐
                              │   TENANTS    │ (Organizações)
                              │──────────────│
                              │ id [PK]      │
                              │ uuid         │
                              │ domain       │
                              │ status       │
                              └──────┬───────┘
                                     │
                ┌────────────────────┼────────────────────┐
                │                    │                    │
                │                    │                    │
        ┌───────▼───────┐   ┌────────▼────────┐  ┌───────▼────────┐
        │     USERS      │   │    WEDDINGS     │  │ SUBSCRIPTIONS  │
        │────────────────│   │─────────────────│  │────────────────│
        │ id [PK]        │   │ id [PK]         │  │ id [PK]        │
        │ tenant_id [FK] │   │ tenant_id [FK]  │  │ tenant_id [FK] │
        │ email          │   │ template_id [FK]│  │ amount_paid    │
        │ password_hash  │   │ bride_name      │  │ starts_at      │
        │ role           │   │ groom_name      │  │ expires_at     │
        └────────────────┘   │ wedding_date    │  │ payment_status │
                             │ status          │  │ pix_code       │
                             │ pix_key         │  └────────────────┘
                             └────────┬────────┘
                                      │
                     ┌────────────────┼────────────────┐
                     │                │                │
             ┌───────▼────────┐ ┌────▼──────────┐ ┌──▼──────────┐
             │   PRESENTES     │ │    RECADOS    │ │   GALLERY   │
             │─────────────────│ │───────────────│ │─────────────│
             │ id [PK]         │ │ id [PK]       │ │ id [PK]     │
             │ wedding_id [FK] │ │ wedding_id    │ │ wedding_id  │
             │ titulo          │ │ nome          │ │ filename    │
             │ valor           │ │ mensagem      │ │ file_path   │
             │ status          │ └───────────────┘ └─────────────┘
             └────────┬────────┘
                      │
                      │
              ┌───────▼─────────┐
              │ PIX_TRANSACTIONS│
              │─────────────────│
              │ id [PK]         │
              │ wedding_id [FK] │
              │ gift_id [FK]    │
              │ amount          │
              │ donor_name      │
              │ status          │
              │ pix_code        │
              └─────────────────┘

        ┌──────────────┐
        │  TEMPLATES   │ (Layouts)
        │──────────────│
        │ id [PK]      │
        │ name         │
        │ slug         │
        │ category     │
        └──────────────┘
```

---

## 🔗 Relacionamentos Detalhados

### 1. TENANTS (Núcleo do Multi-tenancy)

**Relacionamentos:**
- `1:N` com `USERS` (um tenant tem vários usuários)
- `1:N` com `WEDDINGS` (um tenant tem um ou mais casamentos - futuro)
- `1:N` com `SUBSCRIPTIONS` (histórico de pagamentos)
- `1:N` com `ACTIVITY_LOGS` (auditoria)

**Regras de Negócio:**
- Cada tenant representa um "cliente" (casal)
- Domain único: `maria-joao.meucasamento.com.br`
- Isolamento total de dados (todas queries filtram por tenant_id)

---

### 2. USERS (Autenticação e Autorização)

**Relacionamentos:**
- `N:1` com `TENANTS` (pertence a um tenant, exceto super_admin)
- `1:N` com `ACTIVITY_LOGS`

**Roles:**
- `super_admin`: Acesso ao painel master (tenant_id = NULL)
- `tenant_admin`: Dono do casamento (noivo/noiva)
- `tenant_user`: Colaborador (futuro - ajudante/cerimonialista)

---

### 3. TEMPLATES (Layouts disponíveis)

**Relacionamentos:**
- `1:N` com `WEDDINGS` (um template usado por vários casamentos)

**Categorias:**
- `classic`: Romântico tradicional
- `modern`: Minimalista contemporâneo
- `rustic`: Campestre/boho
- `elegant`: Luxuoso sofisticado
- `minimalist`: Clean ultra-simples

---

### 4. WEDDINGS (Site do casamento)

**Relacionamentos:**
- `N:1` com `TENANTS`
- `N:1` com `TEMPLATES`
- `1:N` com `PRESENTES`
- `1:N` com `PIX_TRANSACTIONS`
- `1:N` com `RECADOS`
- `1:N` com `GALLERY_IMAGES`
- `1:N` com `RSVP`

**Estados (status):**
- `draft`: Em construção (não publicado)
- `published`: Ativo e acessível
- `archived`: Após o casamento (histórico)

**Dados Críticos:**
- `wedding_date`: Usado para calcular countdown
- `pix_key`: Chave PIX do casal para receber presentes
- Cores e fontes: Customização visual
- Módulos: Liga/desliga seções (countdown, galeria, presentes)

---

### 5. SUBSCRIPTIONS (Modelo de Receita)

**Relacionamentos:**
- `N:1` com `TENANTS`

**Tipos de Plano:**
- `upfront`: Pagamento único antecipado (recomendado)
- `monthly`: Recorrência mensal (complexo)
- `custom`: Negociações especiais

**Fluxo de Pagamento:**
1. User escolhe data do casamento
2. Sistema calcula: `meses × R$ 5,99`
3. Gera PIX com valor total
4. Após confirmação: `payment_status = 'paid'`
5. Tenant fica `active` até `expires_at`

**Status:**
- `pending`: Aguardando pagamento
- `paid`: Pago e ativo
- `failed`: Falha no pagamento
- `refunded`: Estorno
- `cancelled`: Cancelado

---

### 6. PRESENTES (Lista de presentes)

**Relacionamentos:**
- `N:1` com `WEDDINGS`
- `1:N` com `PIX_TRANSACTIONS`

**Estados:**
- `disponivel`: Ninguém escolheu ainda
- `reservado`: Alguém está comprando (timeout 30min)
- `comprado`: Pago e confirmado

**Campos Especiais:**
- `quantidade_disponivel`: Permite múltiplas unidades do mesmo presente
- `link_loja`: Redireciona para loja externa (alternativa ao PIX)
- `display_order`: Ordem de exibição customizável

---

### 7. PIX_TRANSACTIONS (Pagamentos de presentes)

**Relacionamentos:**
- `N:1` com `WEDDINGS`
- `N:1` com `PRESENTES` (nullable - contribuição livre)

**Fluxo:**
```
iniciado → aguardando → pre_confirmado → confirmado
                    ↓
                 expired / cancelled
```

**Integração Gateway:**
- `payment_provider`: mercadopago, pagseguro, etc
- `payment_provider_txid`: ID único no gateway
- `webhook_data`: JSON com resposta do webhook

**Expiração:**
- PIX expira em 30 minutos (`expires_at`)
- Status muda para `expired` automaticamente

---

### 8. RECADOS (Mensagens dos convidados)

**Relacionamentos:**
- `N:1` com `WEDDINGS`

**Features:**
- `is_approved`: Moderação (anti-spam futuro)
- `is_featured`: Destacar mensagem especial na home
- Armazena IP e User-Agent (segurança/analytics)

---

### 9. GALLERY_IMAGES (Fotos do casal)

**Relacionamentos:**
- `N:1` com `WEDDINGS`

**Organização:**
- `display_order`: Ordem de exibição
- `is_featured`: Aparece em destaque
- `is_cover`: Imagem de capa do site
- `caption`: Legenda da foto

**Storage:**
- Fase 1: `/uploads/galleries/{wedding_id}/`
- Fase 2: AWS S3 ou Cloudflare R2

---

### 10. RSVP (Confirmação de presença - Fase 2)

**Relacionamentos:**
- `N:1` com `WEDDINGS`

**Dados:**
- `will_attend`: yes/no/maybe
- `number_of_guests`: Quantos acompanhantes
- `dietary_restrictions`: Restrições alimentares
- Estatísticas: Total confirmados vs esperados

---

### 11. ACTIVITY_LOGS (Auditoria)

**Relacionamentos:**
- `N:1` com `TENANTS`
- `N:1` com `USERS`

**Eventos Rastreados:**
- `wedding.created`
- `wedding.published`
- `gift.added`
- `gift.purchased`
- `message.received`
- `subscription.paid`
- `user.login`

**Dados JSON:**
- `old_values`: Estado anterior
- `new_values`: Estado novo
- Permite reverter mudanças

---

### 12. DISCOUNT_CODES (Cupons)

**Tipos:**
- `percentage`: 10% de desconto
- `fixed`: R$ 10 de desconto

**Validações:**
- `valid_from` / `valid_until`: Período válido
- `max_uses`: Limite de usos
- `times_used`: Contador

**Uso:**
- Marketing de lançamento
- Parcerias com influencers
- Promoções sazonais

---

### 13. EMAIL_QUEUE (Fila de emails)

**Relacionamentos:**
- `N:1` com `TENANTS`

**Funcionamento:**
- Emails não são enviados diretamente
- Inseridos na fila com prioridade
- Cron job processa fila a cada 1 min
- Retry automático em caso de falha

**Templates:**
- `welcome`: Boas-vindas após cadastro
- `payment_confirmed`: Pagamento aprovado
- `gift_received`: Novo presente recebido
- `wedding_reminder`: Lembrete 1 semana antes

---

## 📊 Cardinalidades Principais

```
TENANTS (1) ──────< (N) USERS
TENANTS (1) ──────< (N) WEDDINGS
TENANTS (1) ──────< (N) SUBSCRIPTIONS

TEMPLATES (1) ──────< (N) WEDDINGS

WEDDINGS (1) ──────< (N) PRESENTES
WEDDINGS (1) ──────< (N) PIX_TRANSACTIONS
WEDDINGS (1) ──────< (N) RECADOS
WEDDINGS (1) ──────< (N) GALLERY_IMAGES
WEDDINGS (1) ──────< (N) RSVP

PRESENTES (1) ──────< (N) PIX_TRANSACTIONS [OPCIONAL]
```

---

## 🔐 Isolamento de Dados (Multi-tenancy)

**Estratégia:** Row-Level Isolation

Toda query deve incluir filtro por tenant:
```sql
-- ❌ ERRADO (vaza dados)
SELECT * FROM presentes WHERE id = 1;

-- ✅ CORRETO
SELECT * FROM presentes 
WHERE id = 1 
  AND wedding_id IN (
    SELECT id FROM weddings WHERE tenant_id = ?
  );
```

**Middleware PHP:**
```php
class TenantMiddleware {
    public static function getCurrentTenant() {
        $subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];
        return Tenant::where('domain', $subdomain)->first();
    }
}
```

---

## 📈 Índices Críticos para Performance

```sql
-- Identificação de tenant
CREATE INDEX idx_tenant_domain ON tenants(domain);
CREATE INDEX idx_tenant_uuid ON tenants(uuid);

-- Queries frequentes
CREATE INDEX idx_weddings_tenant_status ON weddings(tenant_id, status);
CREATE INDEX idx_presentes_wedding_status ON presentes(wedding_id, status);
CREATE INDEX idx_pix_wedding_status ON pix_transactions(wedding_id, status);

-- Busca temporal
CREATE INDEX idx_weddings_date ON weddings(wedding_date);
CREATE INDEX idx_subscriptions_expires ON subscriptions(expires_at);
CREATE INDEX idx_pix_created ON pix_transactions(created_at);

-- Lookups comuns
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_templates_slug ON templates(slug);
```

---

## 🎯 Queries Comuns

### 1. Dashboard do Noivo
```sql
SELECT 
  COUNT(DISTINCT p.id) AS total_presentes,
  COUNT(DISTINCT CASE WHEN p.status = 'comprado' THEN p.id END) AS comprados,
  COALESCE(SUM(CASE WHEN pt.status = 'confirmado' THEN pt.amount END), 0) AS total_arrecadado,
  COUNT(DISTINCT r.id) AS total_recados
FROM weddings w
LEFT JOIN presentes p ON w.id = p.wedding_id AND p.deletado = 0
LEFT JOIN pix_transactions pt ON w.id = pt.wedding_id
LEFT JOIN recados r ON w.id = r.wedding_id
WHERE w.id = ? AND w.tenant_id = ?;
```

### 2. Dashboard Super Admin
```sql
SELECT 
  COUNT(DISTINCT t.id) AS total_tenants,
  COUNT(DISTINCT CASE WHEN t.status = 'active' THEN t.id END) AS ativos,
  COUNT(DISTINCT w.id) AS total_casamentos,
  COUNT(DISTINCT CASE WHEN w.status = 'published' THEN w.id END) AS publicados,
  COALESCE(SUM(s.amount_paid), 0) AS receita_total,
  COALESCE(SUM(CASE WHEN s.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN s.amount_paid END), 0) AS receita_mes
FROM tenants t
LEFT JOIN weddings w ON t.id = w.tenant_id
LEFT JOIN subscriptions s ON t.id = s.tenant_id AND s.payment_status = 'paid';
```

### 3. Validar Assinatura Ativa
```sql
SELECT 
  CASE 
    WHEN s.expires_at >= CURDATE() AND s.payment_status = 'paid' 
    THEN TRUE 
    ELSE FALSE 
  END AS subscription_active
FROM tenants t
LEFT JOIN subscriptions s ON t.id = s.tenant_id
WHERE t.id = ?
ORDER BY s.expires_at DESC
LIMIT 1;
```

---

## 🛠️ Ferramentas Recomendadas para Visualização

Se quiser gerar diagrama visual bonito:

1. **dbdiagram.io** (recomendado)
   - Cole o SQL e gera diagrama automático
   - Export PNG/PDF

2. **MySQL Workbench**
   - Reverse Engineering do banco
   - Gera DER visual

3. **DrawSQL**
   - Interface drag-and-drop
   - Compartilhável

4. **DBeaver**
   - Client SQL com ER Diagram nativo

---

**Versão:** 1.0  
**Data:** 28/10/2025  
**Status:** Documentação

