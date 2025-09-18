<?php
/**
 * API para envio de recados via AJAX
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../functions/recados_db.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit;
}

// Verificar se é JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'JSON inválido'
    ]);
    exit;
}

// Validar dados
$nome = sanitizeInput($data['nome'] ?? '');
$mensagem = sanitizeInput($data['mensagem'] ?? '');

$errors = validateRecadoData($nome, $mensagem);

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// Salvar recado
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

if (addRecado($nome, $mensagem, $ipAddress, $userAgent)) {
    echo json_encode([
        'success' => true,
        'message' => 'Muito obrigado pelo seu carinho! Ficamos emocionados com sua mensagem e mal podemos esperar para celebrar este momento especial ao seu lado. Seu recado enche nossos corações de alegria! 💕'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ops! Algo deu errado ao enviar seu recado. Pode tentar novamente?'
    ]);
}
?>
