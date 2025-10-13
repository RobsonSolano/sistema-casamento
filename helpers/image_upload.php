<?php
/**
 * Helper para Upload de Imagens dos Presentes
 */

// Diretório onde as imagens serão salvas
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', dirname(__DIR__) . '/assets/images/presentes/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', 'assets/images/presentes/');
}

// Configurações de upload
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
}
if (!defined('ALLOWED_TYPES')) {
    define('ALLOWED_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif']);
}
if (!defined('ALLOWED_EXTENSIONS')) {
    define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
}

/**
 * Cria o diretório de upload se não existir
 */
function createUploadDirectory() {
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
}

/**
 * Faz upload de uma imagem
 * @param array $file - Array do $_FILES
 * @return array - ['success' => bool, 'filename' => string|null, 'error' => string|null]
 */
function uploadGiftImage($file) {
    createUploadDirectory();
    
    // Verificar se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'filename' => null,
            'error' => 'Erro ao fazer upload da imagem.'
        ];
    }
    
    // Verificar tamanho do arquivo
    if ($file['size'] > MAX_FILE_SIZE) {
        return [
            'success' => false,
            'filename' => null,
            'error' => 'Arquivo muito grande. Tamanho máximo: 5MB.'
        ];
    }
    
    // Verificar tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_TYPES)) {
        return [
            'success' => false,
            'filename' => null,
            'error' => 'Tipo de arquivo não permitido. Use: JPG, PNG, WEBP ou GIF.'
        ];
    }
    
    // Verificar extensão
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return [
            'success' => false,
            'filename' => null,
            'error' => 'Extensão de arquivo não permitida.'
        ];
    }
    
    // Gerar nome único para o arquivo
    $filename = uniqid('present_') . '_' . time() . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    // Mover arquivo para o diretório de upload
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => true,
            'filename' => $filename,
            'error' => null
        ];
    } else {
        return [
            'success' => false,
            'filename' => null,
            'error' => 'Erro ao salvar imagem no servidor.'
        ];
    }
}

/**
 * Deleta uma imagem do servidor
 * @param string $filename - Nome do arquivo
 * @return bool
 */
function deleteGiftImage($filename) {
    if (empty($filename)) {
        return true;
    }
    
    $filepath = UPLOAD_DIR . $filename;
    
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return true;
}

/**
 * Obtém a URL completa da imagem
 * @param string|null $filename - Nome do arquivo
 * @return string|null
 */
function getGiftImageUrl($filename) {
    if (empty($filename)) {
        return null;
    }
    
    return BASE_URL . '/' . UPLOAD_URL . $filename;
}

/**
 * Verifica se a imagem existe
 * @param string $filename - Nome do arquivo
 * @return bool
 */
function giftImageExists($filename) {
    if (empty($filename)) {
        return false;
    }
    
    return file_exists(UPLOAD_DIR . $filename);
}
?>

