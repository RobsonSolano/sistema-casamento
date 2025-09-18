<?php
/**
 * Funções auxiliares do sistema
 * Arquivo com funções utilitárias para o projeto
 */

/**
 * Sanitiza dados de entrada
 * @param mixed $data
 * @return mixed
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida email
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Gera token seguro
 * @param int $length
 * @return string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Criptografa dados
 * @param string $data
 * @return string
 */
function encryptData($data) {
    $key = ENCRYPTION_KEY;
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Descriptografa dados
 * @param string $data
 * @return string|false
 */
function decryptData($data) {
    $key = ENCRYPTION_KEY;
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

/**
 * Formata data para exibição
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Formata moeda brasileira
 * @param float $value
 * @return string
 */
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Redimensiona imagem
 * @param string $source
 * @param string $destination
 * @param int $width
 * @param int $height
 * @return bool
 */
function resizeImage($source, $destination, $width, $height) {
    $imageInfo = getimagesize($source);
    
    if (!$imageInfo) {
        return false;
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // Criar imagem de origem
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    // Criar imagem de destino
    $destinationImage = imagecreatetruecolor($width, $height);
    
    // Preservar transparência para PNG
    if ($mimeType === 'image/png') {
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);
        $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
        imagefilledrectangle($destinationImage, 0, 0, $width, $height, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
    
    // Salvar imagem
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($destinationImage, $destination, 90);
            break;
        case 'image/png':
            $result = imagepng($destinationImage, $destination, 9);
            break;
        case 'image/gif':
            $result = imagegif($destinationImage, $destination);
            break;
    }
    
    // Limpar memória
    imagedestroy($sourceImage);
    imagedestroy($destinationImage);
    
    return $result;
}

/**
 * Upload de arquivo
 * @param array $file
 * @param string $destination
 * @param array $allowedTypes
 * @return array
 */
function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    if (!isset($file['error']) || is_array($file['error'])) {
        $result['message'] = 'Arquivo inválido';
        return $result;
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            $result['message'] = 'Nenhum arquivo enviado';
            return $result;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $result['message'] = 'Arquivo muito grande';
            return $result;
        default:
            $result['message'] = 'Erro desconhecido';
            return $result;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        $result['message'] = 'Arquivo muito grande';
        return $result;
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        $result['message'] = 'Tipo de arquivo não permitido';
        return $result;
    }
    
    $filename = uniqid() . '.' . $fileExtension;
    $filepath = $destination . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['message'] = 'Erro ao salvar arquivo';
        return $result;
    }
    
    $result['success'] = true;
    $result['filename'] = $filename;
    $result['message'] = 'Arquivo enviado com sucesso';
    
    return $result;
}

/**
 * Envia email
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $from
 * @return bool
 */
function sendEmail($to, $subject, $message, $from = '') {
    if (empty($from)) {
        $from = SMTP_USER;
    }
    
    $headers = [
        'From: ' . $from,
        'Reply-To: ' . $from,
        'X-Mailer: PHP/' . phpversion(),
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Log de erros
 * @param string $message
 * @param string $level
 */
function logError($message, $level = 'ERROR') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;
    file_put_contents('logs/error.log', $logMessage, FILE_APPEND | LOCK_EX);
}

/**
 * Resposta JSON
 * @param mixed $data
 * @param int $statusCode
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Verifica se é requisição AJAX
 * @return bool
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Gera slug a partir de string
 * @param string $string
 * @return string
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Valida CPF
 * @param string $cpf
 * @return bool
 */
function validateCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) !== 11) {
        return false;
    }
    
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    
    return true;
}

/**
 * Valida CNPJ
 * @param string $cnpj
 * @return bool
 */
function validateCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    if (strlen($cnpj) !== 14) {
        return false;
    }
    
    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }
    
    for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
        $sum += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    
    $remainder = $sum % 11;
    $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
    
    if ($cnpj[12] != $digit1) {
        return false;
    }
    
    for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
        $sum += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    
    $remainder = $sum % 11;
    $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
    
    return $cnpj[13] == $digit2;
}

/**
 * Gera URL completa baseada na URL base do projeto
 * @param string $uri URI relativa (ex: 'presentes', '/presentes', 'admin/login')
 * @return string URL completa
 */
function base_url($uri = '') {
    // Remove barra inicial se existir
    $uri = ltrim($uri, '/');
    
    // Se não há URI, retorna apenas a BASE_URL
    if (empty($uri)) {
        return BASE_URL;
    }
    
    // Retorna BASE_URL + URI
    return BASE_URL . '/' . $uri;
}

/**
 * Gera URL para assets (CSS, JS, imagens)
 * @param string $path Caminho do asset
 * @return string URL completa do asset
 */
function asset_url($path) {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Gera URL para rotas do sistema
 * @param string $route Rota (ex: 'presentes', 'login', 'admin')
 * @return string URL completa da rota
 */
function route_url($route) {
    return base_url($route);
}

/**
 * Redireciona para uma URL
 * @param string $url URL de destino
 * @param int $statusCode Código de status HTTP
 */
function redirect($url, $statusCode = 302) {
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Redireciona para uma rota do sistema
 * @param string $route Rota de destino
 * @param int $statusCode Código de status HTTP
 */
function redirect_to($route, $statusCode = 302) {
    redirect(route_url($route), $statusCode);
}
?>
