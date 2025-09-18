# 🚀 Sistema de Rotas Amigáveis

Este projeto agora possui um sistema de rotas amigáveis que facilita o acesso para todos os usuários, especialmente pessoas leigas e idosos.

## 📍 Rotas Disponíveis

### 🏠 Página Principal
- **URL:** `/` ou `/index.php`
- **Descrição:** Página inicial com informações do casal e botão para ver presentes
- **Arquivo:** `home.php`

### 🎁 Lista de Presentes
- **URL:** `/presentes`
- **Descrição:** Lista completa de presentes disponíveis
- **Arquivo:** `lista.php`

### 🔐 Área Administrativa
- **URL:** `/login`
- **Descrição:** Página de login para administradores
- **Arquivo:** `admin/login.php`

- **URL:** `/admin`
- **Descrição:** Painel administrativo principal
- **Arquivo:** `admin/dashboard.php`

- **URL:** `/admin/simple`
- **Descrição:** Dashboard administrativo simplificado
- **Arquivo:** `admin/dashboard_simple.php`

## 🛠️ Como Funciona

### 1. Arquivo `.htaccess`
- Configura as regras de reescrita de URL
- Redireciona URLs amigáveis para os arquivos corretos
- Inclui configurações de segurança e performance

### 2. Arquivo `router.php`
- Classe Router para controle centralizado de rotas
- Funções helper para gerar URLs e redirecionamentos
- Facilita manutenção e adição de novas rotas

### 3. Arquivo `index.php`
- Ponto de entrada principal do sistema
- Processa todas as requisições
- Exibe página 404 para rotas não encontradas

## 📱 Vantagens para Usuários

### ✅ Facilidade de Uso
- URLs simples e intuitivas
- Não precisa lembrar nomes de arquivos
- Navegação mais natural

### ✅ Acessibilidade
- Ideal para pessoas leigas em tecnologia
- URLs fáceis de compartilhar
- Funciona bem em dispositivos móveis

### ✅ SEO e Compartilhamento
- URLs amigáveis para mecanismos de busca
- Melhor experiência ao compartilhar links
- URLs mais profissionais

## 🔧 Manutenção

### Adicionar Nova Rota
1. Editar `router.php` e adicionar nova rota:
```php
$router->addRoute('/nova-rota', 'arquivo.php', 'Título da Página');
```

2. Adicionar regra no `.htaccess`:
```apache
RewriteRule ^nova-rota/?$ arquivo.php [L]
```

### Modificar Rota Existente
- Alterar apenas o arquivo `router.php`
- As mudanças são aplicadas automaticamente

## 🚨 Tratamento de Erros

- **404:** Página não encontrada com link para voltar ao início
- **500:** Erro interno com mensagem clara
- **Proteção:** Arquivos sensíveis protegidos por `.htaccess`

## 📋 Exemplos de Uso

### Para Convidados:
- Acessar: `seusite.com/` (página inicial)
- Ver presentes: `seusite.com/presentes`

### Para Administradores:
- Login: `seusite.com/login`
- Painel: `seusite.com/admin`

## 🔒 Segurança

- Arquivos de configuração protegidos
- Headers de segurança configurados
- Validação de rotas implementada
- Proteção contra acesso direto a arquivos sensíveis

---

**💡 Dica:** Este sistema torna o projeto muito mais profissional e fácil de usar para todos os tipos de usuários!
