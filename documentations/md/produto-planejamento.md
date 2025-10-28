# MeuCasamento.com.br - Documentação do Produto SaaS

## 📋 Visão Geral do Produto

Plataforma SaaS multi-tenant que permite noivos criarem sites personalizados para seus casamentos com sistema integrado de lista de presentes, recados e pagamento via PIX.

## 💡 Análise de Viabilidade

### ✅ Pontos Fortes
- **Mercado validado**: Zankyou, Casar.com cobram caro ou têm limitações
- **Problema real**: Noivos precisam de solução simples e acessível
- **Modelo de receita claro**: R$ 5,99/mês é acessível vs concorrentes (R$ 100-300)
- **Tecnologia já desenvolvida**: Você já tem o core (PIX, presentes, recados)
- **Baixo custo operacional**: Servidor compartilhado, sem estoque

### ⚠️ Desafios
- **Sazonalidade**: Pico em certos meses (dez, jan, mar)
- **Ciclo curto**: Cliente usa 6-12 meses e cancela
- **Suporte**: Noivos podem ser exigentes
- **Marketing**: Precisa investir em SEO/Ads

### 💰 Modelo de Receita Recomendado

**Sugestão: Cobrança única antecipada**

```
Preço = R$ 5,99 × meses até o casamento (mínimo 1 mês)

Exemplos:
- Casamento em 1 mês: R$ 5,99
- Casamento em 6 meses: R$ 35,94
- Casamento em 12 meses: R$ 71,88
```

**Vantagens:**
- Elimina inadimplência
- Fluxo de caixa imediato
- Menos complexidade técnica (sem recorrência)
- Reduz churn involuntário

**Alternativa:** Planos fixos
- **Express** (até 3 meses): R$ 29,90
- **Tradicional** (até 6 meses): R$ 49,90
- **Planejado** (até 12 meses): R$ 79,90

---

## 🔄 Fluxo de Dados

### 1. Onboarding do Cliente

```
┌──────────────┐
│ Landing Page │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Criar Conta  │ → users (tenant_admin)
└──────┬───────┘   tenants (new)
       │
       ▼
┌──────────────────┐
│ Dados Casamento  │ → weddings (draft)
│ (wizard/steps)   │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Escolher Template│ → weddings.template_id
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Customizar Cores │ → weddings (colors)
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Preview Site     │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Checkout         │ → subscriptions
│ (R$ 5,99/mês)    │   (pending)
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Pagamento PIX    │ → subscriptions
└──────┬───────────┘   (paid)
       │
       ▼
┌──────────────────┐
│ Site Publicado   │ → weddings (published)
│ maria-joao.      │   tenants (active)
│ meucasamento.    │
│ com.br           │
└──────────────────┘
```

### 2. Fluxo de Presente (PIX)

```
Convidado acessa site
  ↓
Navega na lista de presentes
  ↓
Escolhe presente
  ↓
Preenche nome/contato
  ↓
Sistema gera PIX dinâmico → pix_transactions (iniciado)
  ↓
Exibe QR Code + Código Copia e Cola
  ↓
Convidado paga
  ↓
Webhook/Pooling detecta pagamento → pix_transactions (confirmado)
  ↓
Atualiza presente → presentes (comprado)
  ↓
Notifica noivos (email/SMS)
```

### 3. Painel Administrativo Master

```
Super Admin Login
  ↓
Dashboard com métricas:
  - Total tenants (ativos, suspensos, cancelados)
  - Total weddings (publicados, drafts)
  - Total presentes cadastrados
  - Receita total (subscriptions.sum)
  - Receita mês atual
  - Taxa de conversão (cadastros → pagos)
  - Churn rate
  ↓
Gestão de Tenants:
  - Visualizar detalhes
  - Suspender/Reativar
  - Acessar como (impersonate)
  - Customizar manualmente
  ↓
Gestão de Templates:
  - Upload novos templates
  - Ativar/Desativar
  - Preview
```

---

## 🔌 Dependências de Serviços Terceiros

### Essenciais (MVP)

1. **Gateway de Pagamento**
   - **Recomendado:** Mercado Pago (API PIX)
   - Alternativas: PagSeguro, Stripe (internacional)
   - Necessário: Webhooks para confirmação automática
   - Custo: ~4% por transação

2. **Envio de E-mails**
   - **Recomendado:** SendGrid (100 emails/dia free)
   - Alternativas: Mailgun, Amazon SES
   - Uso: Confirmações, notificações, recuperação de senha

3. **Armazenamento de Imagens**
   - **Fase 1:** Servidor local (já tem)
   - **Fase 2:** AWS S3 / Cloudflare R2
   - Necessário para: Fotos galeria, presentes, QR codes

4. **SSL/HTTPS**
   - **Recomendado:** Let's Encrypt (grátis)
   - Necessário para subdomínios wildcard

5. **DNS**
   - Cloudflare (grátis + CDN)
   - Necessário para: Subdomínios dinâmicos (*.meucasamento.com.br)

### Opcionais (Fase 2)

6. **SMS** (notificações)
   - Twilio
   - Zenvia

7. **Analytics**
   - Google Analytics
   - Plausible (privacy-first)

8. **Monitoramento**
   - Sentry (error tracking)
   - UptimeRobot (uptime monitoring)

9. **Backup**
   - Automático diário do banco
   - S3 Glacier (long-term)

10. **CDN**
    - Cloudflare (grátis)
    - Melhora performance global

---

## 🛠️ Roadmap de Implementação

### Fase 1: MVP (3-4 meses)

**Mês 1-2: Infraestrutura Multi-tenant**
- [ ] Refatorar código para suportar multi-tenancy
- [ ] Criar novas tabelas do banco
- [ ] Sistema de autenticação/autorização
- [ ] Subdomínios dinâmicos (*.meucasamento.com.br)

**Mês 2-3: Portal do Cliente**
- [ ] Landing page do produto
- [ ] Sistema de cadastro e login
- [ ] Wizard de onboarding (5 passos)
- [ ] Seleção de template (3 opções)
- [ ] Customização de cores
- [ ] Preview em tempo real
- [ ] Checkout e integração PIX (Mercado Pago)

**Mês 3-4: Área Administrativa Cliente**
- [ ] Dashboard do noivo
- [ ] CRUD de presentes
- [ ] Visualização de recados
- [ ] Acompanhamento PIX
- [ ] Upload de fotos galeria
- [ ] Edição de textos do site

**Mês 4: Painel Master + Testes**
- [ ] Dashboard super admin
- [ ] Gestão de tenants
- [ ] Métricas e relatórios
- [ ] Testes end-to-end
- [ ] Beta com 5-10 casais

### Fase 2: Crescimento (mês 5-8)

- [ ] 2 templates adicionais (total 5)
- [ ] Domínio customizado (nosso-casamento.com)
- [ ] Lista de presença (RSVP)
- [ ] Notificações SMS
- [ ] App mobile (PWA)
- [ ] Exportação de dados
- [ ] Integração com Google Calendar
- [ ] SEO otimizado por tenant

### Fase 3: Scale (mês 9+)

- [ ] API pública
- [ ] Marketplace de templates (creators)
- [ ] Plano premium (recursos extras)
- [ ] White-label para cerimonialistas
- [ ] Integrações (Instagram, Google Photos)
- [ ] IA para sugestão de presentes
- [ ] Análise de comportamento dos convidados

---

## 💻 Arquitetura Técnica

### Stack Atual (Manter)
- PHP 8.2+
- MySQL 8.4
- Vanilla JS
- CSS moderno

### Melhorias Necessárias

1. **Multi-tenancy**
   - Isolamento por `tenant_id` em todas queries
   - Middleware de identificação de tenant (por subdomain)
   - Fallback para domínios customizados

2. **Segurança**
   - JWT ou Session com refresh tokens
   - Rate limiting (evitar spam)
   - CSRF protection
   - XSS sanitization
   - SQL Injection prevention (prepared statements)
   - File upload validation (imagens)

3. **Performance**
   - Cache (Redis/Memcached)
   - Lazy loading de imagens
   - Minificação CSS/JS
   - Database indexing otimizado
   - CDN para assets estáticos

4. **Estrutura de Pastas**
```
/public_html
  /meucasamento.com.br (landing + marketing)
  /app (aplicação SaaS)
    /admin (super admin)
    /dashboard (área do noivo)
    /site (sites públicos dos casamentos)
  /api (endpoints REST)
  /templates (layouts disponíveis)
    /classic
    /modern
    /rustic
```

---

## 📊 Métricas de Sucesso

### KPIs Fase MVP
- 50 cadastros
- 20 conversões (40% conversion rate)
- R$ 1.000 MRR (Monthly Recurring Revenue)
- NPS > 8

### KPIs Ano 1
- 500 casamentos publicados
- R$ 15.000 MRR
- 70% retention (não cancelar antes do casamento)
- < 2% churn mensal

---

## 💰 Projeção Financeira Simplificada

### Custos Mensais Estimados
- Servidor (VPS): R$ 150
- Domínio: R$ 5
- SSL: R$ 0 (Let's Encrypt)
- E-mail (SendGrid): R$ 0 (até 100/dia)
- Gateway: 4% das transações
- **Total fixo:** R$ 155/mês

### Receita (Cenário Conservador)
- Mês 1: 5 clientes × R$ 35,94 (6 meses médios) = R$ 179,70
- Mês 3: 15 clientes × R$ 35,94 = R$ 539,10
- Mês 6: 40 clientes × R$ 35,94 = R$ 1.437,60
- Mês 12: 100 clientes × R$ 35,94 = R$ 3.594,00

**Break-even:** ~5 clientes/mês

---

## ⚠️ Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| Baixa conversão | Alta | Alto | Beta gratuito, marketing agressivo |
| Concorrência grande | Média | Médio | Preço competitivo, UX superior |
| Problemas técnicos | Média | Alto | Testes extensivos, monitoring |
| Fraude em PIX | Baixa | Médio | Validação rigorosa, limite de valor |
| Churn alto | Média | Alto | Suporte proativo, feature engagement |
| Sazonalidade | Alta | Médio | Diversificar (aniversários, eventos) |

---

## 🎯 Próximos Passos Imediatos

1. **Validação de Mercado**
   - Pesquisa com 20 noivos (Google Forms)
   - Análise de concorrentes (pricing, features)
   - Registrar domínio meucasamento.com.br

2. **Planejamento Técnico**
   - Definir arquitetura multi-tenant
   - Escolher gateway de pagamento
   - Criar protótipo de 1 template

3. **Legal/Administrativo**
   - CNPJ (MEI ou ME)
   - Contrato de serviço (termos de uso)
   - LGPD compliance
   - Política de privacidade

4. **Marketing Pre-Launch**
   - Landing page "coming soon"
   - Lista de espera (early adopters)
   - Parcerias com cerimonialistas

---

## 📝 Conclusão

**Viabilidade:** ⭐⭐⭐⭐☆ (4/5)

**Prós:**
- Mercado existe e paga
- Você já tem 60% do código
- Baixo custo operacional
- Escalável

**Contras:**
- Competitivo
- Ciclo de vida curto do cliente
- Precisa de marketing constante

**Recomendação:** SEGUIR EM FRENTE! 🚀

O modelo de cobrança antecipada é MUITO mais inteligente que recorrência mensal. Elimina 90% dos problemas de payment.

---

**Criado em:** 28/10/2025  
**Versão:** 1.0  
**Status:** Planejamento

