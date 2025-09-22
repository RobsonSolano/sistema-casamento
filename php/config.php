<?php
/**
 * Configurações do Sistema
 * Arquivo de configuração principal do projeto
 */

// Configurações do Site
define('SITE_TITLE', 'Lista de Presentes - Casamento');
define('SITE_DESCRIPTION', 'Lista de presentes para o casamento');

// URL Base do Projeto (ajuste conforme necessário)
define('BASE_URL', 'https://php81.nano.docker/_estudos/pessoal/casamento-presentes');

// Informações do Casal
define('COUPLE_NAME_1', 'MARISLAN');
define('COUPLE_NAME_2', 'DOUGLAS');
define('WEDDING_DATE', '13 | 12 | 2025');
define('WELCOME_MESSAGE', 'Estamos muito felizes em compartilhar este momento especial com vocês!');

// Configurações de Pagamento PIX
define('PIX_KEY', '11996271186'); // Chave PIX (celular)
define('PIX_KEY_TYPE', 'phone'); // Tipo da chave PIX
define('PIX_OWNER_NAME', 'MARISLAN E DOUGLAS'); // Nome do titular
define('WHATSAPP_NUMBER', '5511996271186'); // WhatsApp para comprovantes


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
