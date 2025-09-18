<?php
/**
 * API para gerar PIX
 * Endpoint para criar QR Code e código PIX
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../functions/pix.php';

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    // Obter dados do POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Log para debug
    error_log('PIX API - Input recebido: ' . print_r($input, true));
    
    if (!$input) {
        throw new Exception('Dados inválidos');
    }
    
    $giftId = $input['gift_id'] ?? null;
    $amount = $input['amount'] ?? null;
    $donorName = $input['donor_name'] ?? '';
    $donorPhone = $input['donor_phone'] ?? '';
    
    // Validações
    if (!$giftId || !$amount) {
        throw new Exception('ID do presente e valor são obrigatórios');
    }
    
    if (!is_numeric($amount) || $amount <= 0) {
        throw new Exception('Valor deve ser um número positivo');
    }
    
    // Buscar dados do presente
    require_once __DIR__ . '/../functions/gifts_db.php';
    $gift = getGiftById($giftId);
    
    if (!$gift) {
        throw new Exception('Presente não encontrado');
    }
    
    // Gerar PIX
    error_log('PIX API - Gerando PIX para valor: ' . $amount);
    $pixData = generatePixCode($amount, "Presente: {$gift['title']}");
    error_log('PIX API - PIX gerado: ' . print_r($pixData, true));
    
    // Gerar QR Code
    $qrCodeUrl = generatePixQRCode($pixData['code']);
    error_log('PIX API - QR Code URL: ' . $qrCodeUrl);
    
    // Salvar transação no banco
    $transactionSaved = savePixTransaction(
        $giftId, 
        $amount, 
        $pixData['transaction_id'], 
        $donorName, 
        $donorPhone
    );
    
    if (!$transactionSaved) {
        throw new Exception('Erro ao salvar transação');
    }
    
    // Gerar mensagem para WhatsApp
    $whatsappMessage = generateComprovanteMessage(
        $gift['title'], 
        $amount, 
        $pixData['transaction_id']
    );
    
    $whatsappLink = formatWhatsAppLink(WHATSAPP_NUMBER, $whatsappMessage);
    
    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'data' => [
            'transaction_id' => $pixData['transaction_id'],
            'amount' => $amount,
            'formatted_amount' => $pixData['formatted_amount'],
            'gift_name' => $gift['title'],
            'pix_code' => $pixData['code'],
            'qr_code_url' => $qrCodeUrl,
            'whatsapp_link' => $whatsappLink,
            'whatsapp_message' => $whatsappMessage
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
