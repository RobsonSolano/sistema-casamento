<?php
/**
 * Configurações do Sistema
 * Arquivo de configuração principal do projeto
 */

// Configurações do Site
define('SITE_TITLE', 'Lista de Presentes - Casamento');
define('SITE_URL', 'http://localhost/casamento-presentes');
define('SITE_DESCRIPTION', 'Lista de presentes para o casamento');

// Informações do Casal
define('COUPLE_NAME_1', 'Maria');
define('COUPLE_NAME_2', 'João');
define('WEDDING_DATE', '15 de Dezembro de 2024');
define('WELCOME_MESSAGE', 'Estamos muito felizes em compartilhar este momento especial com vocês!');

// Configurações de Banco de Dados
define('DB_HOST', 'mysql');
define('DB_NAME', 'pessoal_casamento_mari_douglas');
define('DB_USER', 'root');
define('DB_PASS', '123');

// Configurações de Email (se necessário futuramente)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Configurações de Upload
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configurações de Segurança
define('ENCRYPTION_KEY', 'your-secret-key-here');
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configurações de Ambiente
define('ENVIRONMENT', 'development'); // development, production
define('DEBUG_MODE', true);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Headers de Segurança
if (ENVIRONMENT === 'production') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
