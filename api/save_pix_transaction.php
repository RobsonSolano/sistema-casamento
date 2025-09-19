<?php
/**
 * API para salvar transação PIX
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Dados inválidos');
    }
    
    $giftId = $input['gift_id'] ?? null;
    $giftName = $input['gift_name'] ?? '';
    $amount = $input['amount'] ?? null;
    $donorName = $input['donor_name'] ?? '';
    $donorPhone = $input['donor_phone'] ?? '';
    $status = $input['status'] ?? 'pre_confirmado';
    
    // Validações
    if (!$giftId || !$amount || !$donorName) {
        throw new Exception('ID do presente, valor e nome são obrigatórios');
    }
    
    if (!is_numeric($amount) || $amount <= 0) {
        throw new Exception('Valor deve ser um número positivo');
    }
    
    if (empty($donorName)) {
        throw new Exception('Nome é obrigatório');
    }
    
    // Validar status
    $validStatuses = ['iniciado', 'pre_confirmado', 'confirmado', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Status inválido');
    }
    
    // Conectar ao banco
    $db = Database::getInstance();
    
    // Gerar código PIX simples (apenas para identificação)
    $pixCode = 'PIX_' . time() . '_' . $giftId . '_' . substr(md5($donorName . $amount), 0, 8);
    
    // Inserir transação
    $sql = "INSERT INTO pix_transactions 
            (gift_id, gift_name, amount, pix_code, donor_name, donor_phone, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $result = $db->insert($sql, [
        $giftId,
        $giftName,
        $amount,
        $pixCode,
        $donorName,
        $donorPhone,
        $status
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Transação PIX salva com sucesso',
            'transaction_id' => $result,
            'status' => $status
        ]);
    } else {
        throw new Exception('Erro ao salvar transação no banco de dados');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
