# 💕 Sistema de Presentes para Casamento

Sistema web completo para gerenciamento de lista de presentes de casamento, desenvolvido em PHP 8.3 com Bootstrap 5 e jQuery.

## 🚀 Características Principais

- **Interface moderna e responsiva** com Bootstrap 5
- **Design elegante** com animações CSS e efeitos visuais
- **Música de fundo** com controles de reprodução
- **Sistema PIX integrado** com fluxo completo de pagamentos
- **Área administrativa** para gerenciamento de presentes e transações
- **Sistema de recados** para convidados
- **URLs amigáveis** e sistema de rotas profissional
- **Responsivo** para todos os dispositivos

## 📁 Estrutura do Projeto

```
casamento-presentes/
├── index.php                 # Página principal
├── lista.php                 # Lista completa de presentes
├── composer.json             # Dependências do Composer
├── php/
│   ├── config.php           # Configurações do sistema
│   └── Database.php         # Classe de conexão com banco
├── assets/
│   ├── css/
│   │   └── style.css        # Estilos customizados
│   ├── js/
│   │   ├── main.js          # JavaScript principal
│   │   ├── pix-functions.js # Funções PIX
│   │   └── music-controller.js # Controle de música
│   ├── images/              # Imagens do projeto
│   └── audio/               # Arquivos de áudio
├── admin/
│   ├── dashboard.php        # Painel administrativo
│   ├── pix_transactions.php # Gerenciamento PIX
│   ├── recados.php          # Gerenciamento de recados
│   └── login.php            # Login administrativo
├── api/
│   ├── save_pix_transaction.php # API PIX
│   ├── update_pix_status.php    # API atualização status
│   └── send_recado.php          # API recados
├── functions/
│   ├── gifts_db.php         # Funções de presentes
│   ├── pix.php              # Funções PIX
│   └── recados_db.php       # Funções de recados
├── helpers/
│   └── functions.php        # Funções auxiliares
└── documentations/
    ├── README.md            # Esta documentação
    └── database.sql         # Scripts de banco de dados
```

## 🛠️ Instalação

### Pré-requisitos

- PHP 8.3 ou superior
- MySQL/MariaDB
- Composer (opcional)
- Servidor web (Apache/Nginx) ou PHP built-in server

### Passos de Instalação

1. **Clone ou baixe o projeto**
   ```bash
   git clone [url-do-repositorio]
   cd casamento-presentes
   ```

2. **Instale as dependências (opcional)**
   ```bash
   composer install
   ```

3. **Configure o banco de dados**
   - Crie um banco de dados MySQL
   - Execute os scripts em `documentations/database.sql`
   - Configure as credenciais em `php/config.php`

4. **Configure o projeto**
   - Edite o arquivo `php/config.php` com suas informações
   - Configure a chave PIX e dados do WhatsApp
   - Adicione a foto do casal em `assets/images/casal.jpg`
   - Adicione a música de fundo em `assets/audio/musica.mp3`

5. **Execute o servidor**
   ```bash
   # Usando PHP built-in server
   php -S localhost:8000
   
   # Ou usando Composer
   composer serve
   ```

6. **Acesse o projeto**
   - Abra seu navegador em `http://localhost:8000`

## ⚙️ Configuração

### Personalização Básica

Edite o arquivo `php/config.php` para personalizar:

```php
// Informações do Casal
define('COUPLE_NAME_1', 'Marislan');
define('COUPLE_NAME_2', 'Douglas');
define('WEDDING_DATE', '15 de Dezembro de 2024');

// Configurações PIX
define('PIX_KEY', '11996271186');
define('PIX_KEY_TYPE', 'phone');
define('PIX_OWNER_NAME', 'MARISLAN E DOUGLAS');

// WhatsApp
define('WHATSAPP_NUMBER', '5511996271186');
```

### Sistema de URLs

O projeto usa um sistema centralizado de URLs:

```php
// Configuração base
define('BASE_URL', 'https://php81.nano.docker/_estudos/pessoal/casamento-presentes');

// Uso nas páginas
echo base_url('presentes');        // URL completa para presentes
echo route_url('admin');           // URL para admin
```

## 🎁 Sistema de Presentes

### Funcionalidades

- **Lista de presentes** com valores e descrições
- **Sistema PIX integrado** com fluxo completo
- **Status de transações**: iniciado → pre_confirmado → confirmado
- **Área administrativa** para gerenciamento
- **Estatísticas** de presentes e transações

### Fluxo PIX

1. **Usuário seleciona presente** → Abre modal PIX
2. **Preenche dados pessoais** → Nome obrigatório
3. **Copia chave PIX** → Salva como "iniciado"
4. **Faz pagamento** → Usuário realiza PIX
5. **Envia comprovante** → Link WhatsApp pré-formatado
6. **Confirma envio** → Atualiza para "pre_confirmado"
7. **Admin confirma** → Status final "confirmado"

## 🔐 Área Administrativa

### Acesso

- **URL**: `/admin/login`
- **Credenciais**: Configure em `php/config.php`

### Funcionalidades

- **Dashboard**: Estatísticas gerais
- **Presentes**: Gerenciamento da lista
- **PIX Transactions**: Controle de pagamentos
- **Recados**: Mensagens dos convidados

## 🌐 Sistema de Rotas

### Rotas Disponíveis

- **`/`** → Página principal
- **`/presentes`** → Lista de presentes
- **`/login`** → Login administrativo
- **`/admin`** → Painel administrativo
- **`/admin/pix_transactions`** → Transações PIX
- **`/admin/recados`** → Recados

### Vantagens

- **URLs amigáveis** e profissionais
- **Fácil manutenção** centralizada
- **SEO otimizado**
- **Acessível** para todos os usuários

## 🎨 Personalização Visual

### Cores Principais

```css
/* Cores principais */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.heart-icon {
    color: #e74c3c;
}
```

### Animações

- `fadeInUp`: Animação de entrada do card principal
- `heartbeat`: Animação do coração
- `slideInLeft/Right`: Animações dos nomes

## 🎵 Funcionalidades de Áudio

- **Reprodução automática** após interação do usuário
- **Controles de play/pause** com botão dedicado
- **Atalhos de teclado**:
  - `Espaço`: Alternar música
  - `Escape`: Pausar música

## 📱 Responsividade

O projeto é totalmente responsivo e funciona em:

- **Desktop**: > 768px
- **Tablet**: 576px - 768px
- **Mobile**: < 576px

## 🔧 Desenvolvimento

### Boas Práticas Implementadas

- **Separação de responsabilidades**
- **Código limpo e comentado**
- **Validação de dados**
- **Sanitização de inputs**
- **Tratamento de erros**
- **Sistema de URLs centralizado**
- **APIs RESTful**

### Estrutura de Arquivos

- **PHP**: Lógica do servidor e configurações
- **CSS**: Estilos e animações
- **JavaScript**: Interatividade e funcionalidades
- **APIs**: Endpoints para comunicação frontend/backend
- **Database**: Classes e funções de banco de dados

## 🚧 Funcionalidades Implementadas

- [x] ✅ Sistema de login para administração
- [x] ✅ CRUD completo de presentes
- [x] ✅ Integração com banco de dados
- [x] ✅ Sistema PIX integrado
- [x] ✅ Área administrativa completa
- [x] ✅ Sistema de recados
- [x] ✅ URLs amigáveis
- [x] ✅ Relatórios e estatísticas
- [x] ✅ Sistema de notificações
- [x] ✅ Responsividade completa

## 🔒 Segurança

- **Arquivos de configuração protegidos**
- **Headers de segurança configurados**
- **Validação de rotas implementada**
- **Proteção contra acesso direto a arquivos sensíveis**
- **Sanitização de dados de entrada**
- **Prepared statements** para banco de dados

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor:

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📞 Suporte

Para dúvidas ou suporte, entre em contato através dos issues do repositório.

---

**Desenvolvido com 💕 para celebrar o amor de Marislan e Douglas**
