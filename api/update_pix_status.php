<?php
/**
 * API para atualizar status de transação PIX
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
    
    $transactionId = $input['transaction_id'] ?? null;
    $newStatus = $input['status'] ?? '';
    
    // Validações
    if (!$transactionId) {
        throw new Exception('ID da transação é obrigatório');
    }
    
    if (empty($newStatus)) {
        throw new Exception('Novo status é obrigatório');
    }
    
    // Validar status
    $validStatuses = ['iniciado', 'pre_confirmado', 'confirmado', 'cancelled'];
    if (!in_array($newStatus, $validStatuses)) {
        throw new Exception('Status inválido');
    }
    
    // Conectar ao banco
    $db = Database::getInstance();
    
    // Verificar se a transação existe
    $checkSql = "SELECT id FROM pix_transactions WHERE id = ?";
    $existingTransaction = $db->fetchOne($checkSql, [$transactionId]);
    
    if (!$existingTransaction) {
        throw new Exception('Transação não encontrada');
    }
    
    // Atualizar status
    $updateSql = "UPDATE pix_transactions SET status = ?, updated_at = NOW() WHERE id = ?";
    $result = $db->update($updateSql, [$newStatus, $transactionId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Status atualizado com sucesso',
            'transaction_id' => $transactionId,
            'new_status' => $newStatus
        ]);
    } else {
        throw new Exception('Erro ao atualizar status no banco de dados');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
