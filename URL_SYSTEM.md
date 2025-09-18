# 🌐 Sistema de URLs Centralizado

Sistema profissional de gerenciamento de URLs que resolve problemas de inconsistência e facilita manutenção.

## 🎯 Problemas Resolvidos

### ❌ Antes (Problemático):
```javascript
// JavaScript com URL hardcoded
window.location.href = '/presentes';
```

```php
// PHP com URLs inconsistentes
<a href="lista.php">Link</a>
<a href="/presentes">Outro link</a>
```

### ✅ Agora (Profissional):
```javascript
// JavaScript usando configuração centralizada
window.location.href = window.BASE_URL + '/presentes';
```

```php
// PHP usando funções helper
<a href="<?php echo base_url('presentes'); ?>">Link</a>
<a href="<?php echo route_url('presentes'); ?>">Outro link</a>
```

## 🛠️ Configuração

### 1. BASE_URL no config.php
```php
// URL Base do Projeto (ajuste conforme necessário)
define('BASE_URL', 'http://localhost/casamento-presentes');
```

### 2. Funções Helper Disponíveis

#### `base_url($uri = '')`
Gera URL completa baseada na BASE_URL:
```php
echo base_url();                    // http://localhost/casamento-presentes
echo base_url('presentes');         // http://localhost/casamento-presentes/presentes
echo base_url('/admin/login');      // http://localhost/casamento-presentes/admin/login
```

#### `route_url($route)`
Alias para `base_url()` com foco em rotas:
```php
echo route_url('presentes');        // http://localhost/casamento-presentes/presentes
echo route_url('login');            // http://localhost/casamento-presentes/login
```

#### `asset_url($path)`
Gera URLs para assets (CSS, JS, imagens):
```php
echo asset_url('css/style.css');   // http://localhost/casamento-presentes/assets/css/style.css
echo asset_url('js/main.js');      // http://localhost/casamento-presentes/assets/js/main.js
```

#### `redirect_to($route, $statusCode = 302)`
Redireciona para uma rota:
```php
redirect_to('presentes');          // Redireciona para /presentes
redirect_to('admin', 301);         // Redireciona para /admin com status 301
```

## 📱 JavaScript Integration

### Configuração Global
```html
<script>
    // Configuração global do projeto
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
```

### Uso no JavaScript
```javascript
// Redirecionamento usando configuração
window.location.href = window.BASE_URL + '/presentes';

// Ou usando função helper (se disponível)
window.location.href = buildUrl('presentes');
```

## 🔧 Exemplos Práticos

### Links em Templates
```php
<!-- Antes -->
<a href="lista.php">Ver Presentes</a>
<a href="/admin">Admin</a>

<!-- Agora -->
<a href="<?php echo base_url('presentes'); ?>">Ver Presentes</a>
<a href="<?php echo base_url('admin'); ?>">Admin</a>
```

### Redirecionamentos
```php
// Antes
header("Location: lista.php");

// Agora
redirect_to('presentes');
```

### Assets
```php
<!-- Antes -->
<link rel="stylesheet" href="assets/css/style.css">

<!-- Agora -->
<link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
```

## 🚀 Vantagens

### ✅ Manutenibilidade
- **Uma única configuração** para toda a aplicação
- **Mudanças centralizadas** - altere apenas o `config.php`
- **Consistência** em todos os arquivos

### ✅ Flexibilidade
- **Fácil mudança de ambiente** (dev, staging, production)
- **Suporte a subdomínios** e subpastas
- **URLs absolutas** funcionam em qualquer contexto

### ✅ Profissionalismo
- **Padrão da indústria** seguido
- **Código limpo** e organizado
- **Fácil para outros desenvolvedores** entenderem

## 🔄 Migração

### Para Produção:
1. Altere `BASE_URL` no `config.php`:
```php
define('BASE_URL', 'https://seusite.com');
```

2. Todos os links e redirecionamentos se ajustam automaticamente!

### Para Subpasta:
```php
define('BASE_URL', 'https://seusite.com/casamento');
```

## 📋 Checklist de Implementação

- [x] ✅ BASE_URL definida no config.php
- [x] ✅ Funções helper criadas
- [x] ✅ JavaScript configurado com window.BASE_URL
- [x] ✅ Links PHP atualizados
- [x] ✅ Redirecionamentos corrigidos
- [x] ✅ Sistema de rotas integrado

## 🎉 Resultado

Agora o projeto tem um sistema de URLs **profissional**, **consistente** e **fácil de manter**! 

Não mais URLs hardcoded ou inconsistentes. Tudo centralizado e configurável! 🚀
