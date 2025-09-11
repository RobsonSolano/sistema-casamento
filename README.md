# 💕 Lista de Presentes para Casamento

Sistema web para gerenciamento de lista de presentes de casamento, desenvolvido em PHP 8.3 com Bootstrap 5 e jQuery.

## 🚀 Características

- **Interface moderna e responsiva** com Bootstrap 5
- **Design elegante** com animações CSS e efeitos visuais
- **Música de fundo** com controles de reprodução
- **Estrutura organizada** seguindo boas práticas de desenvolvimento
- **Sistema de notificações** com toast messages
- **Responsivo** para todos os dispositivos

## 📁 Estrutura do Projeto

```
casamento-presentes/
├── index.php                 # Página principal
├── composer.json             # Dependências do Composer
├── README.md                 # Este arquivo
├── php/
│   └── config.php           # Configurações do sistema
├── assets/
│   ├── css/
│   │   └── style.css        # Estilos customizados
│   ├── js/
│   │   └── main.js          # JavaScript principal
│   ├── images/              # Imagens do projeto
│   └── audio/               # Arquivos de áudio
├── helpers/
│   └── functions.php         # Funções auxiliares
└── functions/
    └── gifts.php            # Funções específicas de presentes
```

## 🛠️ Instalação

### Pré-requisitos

- PHP 8.3 ou superior
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

3. **Configure o projeto**
   - Edite o arquivo `php/config.php` com suas informações
   - Adicione a foto do casal em `assets/images/couple-photo.jpg`
   - Adicione a música de fundo em `assets/audio/piano-melody.mp3`

4. **Execute o servidor**
   ```bash
   # Usando PHP built-in server
   php -S localhost:8000
   
   # Ou usando Composer
   composer serve
   ```

5. **Acesse o projeto**
   - Abra seu navegador em `http://localhost:8000`

## ⚙️ Configuração

### Personalização Básica

Edite o arquivo `php/config.php` para personalizar:

```php
// Informações do Casal
define('COUPLE_NAME_1', 'Maria');
define('COUPLE_NAME_2', 'João');
define('WEDDING_DATE', '15 de Dezembro de 2024');
define('WELCOME_MESSAGE', 'Estamos muito felizes em compartilhar este momento especial com vocês!');
```

### Adicionando Imagens

1. **Foto do casal**: Coloque em `assets/images/couple-photo.jpg`
2. **Imagens de presentes**: Coloque em `assets/images/gifts/`

### Adicionando Música

1. Coloque o arquivo de áudio em `assets/audio/piano-melody.mp3`
2. Formatos suportados: MP3, OGG
3. Recomendado: arquivo pequeno e em loop

## 🎨 Personalização Visual

### Cores

As cores principais podem ser alteradas no arquivo `assets/css/style.css`:

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

O projeto inclui várias animações CSS que podem ser personalizadas:

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

- Desktop
- Tablet
- Smartphone

### Breakpoints

- **Desktop**: > 768px
- **Tablet**: 576px - 768px
- **Mobile**: < 576px

## 🔧 Desenvolvimento

### Estrutura de Arquivos

- **PHP**: Lógica do servidor e configurações
- **CSS**: Estilos e animações
- **JavaScript**: Interatividade e funcionalidades
- **Helpers**: Funções utilitárias reutilizáveis

### Boas Práticas Implementadas

- Separação de responsabilidades
- Código limpo e comentado
- Validação de dados
- Sanitização de inputs
- Tratamento de erros

## 🚧 Próximas Funcionalidades

- [ ] Sistema de login para administração
- [ ] CRUD completo de presentes
- [ ] Integração com banco de dados
- [ ] Sistema de notificações por email
- [ ] Relatórios e estatísticas
- [ ] Integração com pagamentos
- [ ] Sistema de convites

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

**Desenvolvido com 💕 para celebrar o amor**
