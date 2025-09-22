<?php
/**
 * Funções PIX simplificadas para admin
 * Apenas funções necessárias para gerenciar transações
 */

require_once __DIR__ . '/../php/Database.php';

/**
 * Atualiza o status de uma transação PIX
 */
function updatePixTransactionStatus($transactionId, $newStatus) {
    try {
        $db = Database::getInstance();
        
        $validStatuses = ['iniciado', 'pre_confirmado', 'confirmado', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            return false;
        }
        
        $sql = "UPDATE pix_transactions SET status = ?, updated_at = NOW() WHERE id = ?";
        return $db->update($sql, [$newStatus, $transactionId]);
        
    } catch (Exception $e) {
        error_log("Erro ao atualizar status PIX: " . $e->getMessage());
        return false;
    }
}

/**
 * Busca todas as transações PIX
 */
function getAllPixTransactions() {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT pt.*, g.titulo as gift_title, g.valor as gift_value 
                FROM pix_transactions pt 
                LEFT JOIN presentes g ON pt.gift_id = g.id 
                ORDER BY pt.created_at DESC";
        
        return $db->fetchAll($sql);
        
    } catch (Exception $e) {
        error_log("Erro ao buscar transações PIX: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca transação PIX por ID
 */
function getPixTransactionById($id) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT pt.*, g.titulo as gift_title, g.valor as gift_value 
                FROM pix_transactions pt 
                LEFT JOIN presentes g ON pt.gift_id = g.id 
                WHERE pt.id = ?";
        
        return $db->fetchOne($sql, [$id]);
        
    } catch (Exception $e) {
        error_log("Erro ao buscar transação PIX: " . $e->getMessage());
        return false;
    }
}

/**
 * Conta transações por status
 */
function countPixTransactionsByStatus($status = null) {
    try {
        $db = Database::getInstance();
        
        if ($status) {
            $sql = "SELECT COUNT(*) as total FROM pix_transactions WHERE status = ?";
            $result = $db->fetchOne($sql, [$status]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM pix_transactions";
            $result = $db->fetchOne($sql);
        }
        
        $count = $result['total'] ?? 0;
        return is_numeric($count) ? (int)$count : 0;
        
    } catch (Exception $e) {
        error_log("Erro ao contar transações PIX: " . $e->getMessage());
        return 0;
    }
}

/**
 * Calcula total de transações confirmadas
 */
function getTotalConfirmedPixAmount() {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT SUM(amount) as total FROM pix_transactions WHERE status = 'confirmado'";
        $result = $db->fetchOne($sql);
        
        $total = $result['total'] ?? 0;
        return is_numeric($total) ? (float)$total : 0;
        
    } catch (Exception $e) {
        error_log("Erro ao calcular total PIX: " . $e->getMessage());
        return 0;
    }
}
?>
