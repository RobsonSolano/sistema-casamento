<?php
/**
 * Funções para gerenciar presentes no banco de dados
 */

require_once __DIR__ . '/../php/Database.php';

/**
 * Busca todos os presentes ativos
 */
function getAllGifts() {
    $db = Database::getInstance();
    $sql = "SELECT * FROM presentes WHERE deletado = 0 ORDER BY data_criacao DESC";
    return $db->fetchAll($sql);
}

/**
 * Busca presente por ID
 */
function getGiftById($id) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM presentes WHERE id = ? AND deletado = 0";
    return $db->fetchOne($sql, [$id]);
}

/**
 * Adiciona novo presente
 */
function addGift($titulo, $valor, $imagem = null) {
    $db = Database::getInstance();
    $sql = "INSERT INTO presentes (titulo, valor, imagem, status, deletado) VALUES (?, ?, ?, 0, 0)";
    return $db->insert($sql, [$titulo, $valor, $imagem]);
}

/**
 * Atualiza presente
 */
function updateGift($id, $titulo, $valor, $status, $imagem = null) {
    $db = Database::getInstance();
    
    if ($imagem !== null) {
        $sql = "UPDATE presentes SET titulo = ?, valor = ?, status = ?, imagem = ? WHERE id = ? AND deletado = 0";
        return $db->update($sql, [$titulo, $valor, $status, $imagem, $id]);
    } else {
        $sql = "UPDATE presentes SET titulo = ?, valor = ?, status = ? WHERE id = ? AND deletado = 0";
        return $db->update($sql, [$titulo, $valor, $status, $id]);
    }
}

/**
 * Marca presente como comprado/disponível
 */
function toggleGiftStatus($id) {
    $db = Database::getInstance();
    
    // Primeiro busca o status atual
    $gift = getGiftById($id);
    if (!$gift) {
        return false;
    }
    
    $newStatus = $gift['status'] == 1 ? 0 : 1;
    $sql = "UPDATE presentes SET status = ? WHERE id = ? AND deletado = 0";
    return $db->update($sql, [$newStatus, $id]);
}

/**
 * Remove presente (soft delete)
 */
function deleteGift($id) {
    $db = Database::getInstance();
    $sql = "UPDATE presentes SET deletado = 1 WHERE id = ?";
    return $db->update($sql, [$id]);
}

/**
 * Busca presentes com filtros
 */
function searchGifts($search = '', $status = '') {
    $db = Database::getInstance();
    $sql = "SELECT * FROM presentes WHERE deletado = 0";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND titulo LIKE ?";
        $params[] = "%{$search}%";
    }
    
    if ($status !== '') {
        $sql .= " AND status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY data_criacao DESC";
    
    return $db->fetchAll($sql, $params);
}

/**
 * Calcula estatísticas dos presentes
 */
function calculateGiftStats() {
    $db = Database::getInstance();
    
    // Total de presentes
    $totalSql = "SELECT COUNT(*) as total FROM presentes WHERE deletado = 0";
    $total = $db->fetchOne($totalSql)['total'];
    
    // Presentes comprados
    $purchasedSql = "SELECT COUNT(*) as purchased FROM presentes WHERE deletado = 0 AND status = 1";
    $purchased = $db->fetchOne($purchasedSql)['purchased'];
    
    // Presentes disponíveis
    $available = $total - $purchased;
    
    // Valor total
    $totalValueSql = "SELECT SUM(valor) as total_value FROM presentes WHERE deletado = 0";
    $totalValue = $db->fetchOne($totalValueSql)['total_value'] ?? 0;
    
    // Valor comprado
    $purchasedValueSql = "SELECT SUM(valor) as purchased_value FROM presentes WHERE deletado = 0 AND status = 1";
    $purchasedValue = $db->fetchOne($purchasedValueSql)['purchased_value'] ?? 0;
    
    // Valor disponível
    $availableValue = $totalValue - $purchasedValue;
    
    // Percentual comprado
    $purchasePercentage = $total > 0 ? round(($purchased / $total) * 100, 2) : 0;
    
    // Total de recados
    $totalRecadosSql = "SELECT COUNT(*) as total_recados FROM recados";
    $totalRecados = $db->fetchOne($totalRecadosSql)['total_recados'] ?? 0;
    
    return [
        'total_gifts' => (int)$total,
        'purchased_gifts' => (int)$purchased,
        'available_gifts' => (int)$available,
        'total_value' => (float)$totalValue,
        'purchased_value' => (float)$purchasedValue,
        'available_value' => (float)$availableValue,
        'purchase_percentage' => $purchasePercentage,
        'total_recados' => (int)$totalRecados
    ];
}

/**
 * Valida dados do presente
 */
function validateGiftData($titulo, $valor) {
    $errors = [];
    
    if (empty($titulo)) {
        $errors[] = 'Título é obrigatório';
    }
    
    if (empty($valor) || !is_numeric($valor) || $valor <= 0) {
        $errors[] = 'Valor deve ser um número positivo';
    }
    
    return $errors;
}

/**
 * Formata presente para exibição
 */
function formatGiftForDisplay($gift) {
    require_once __DIR__ . '/../helpers/image_upload.php';
    
    return [
        'id' => $gift['id'],
        'titulo' => $gift['titulo'],
        'valor' => formatCurrency($gift['valor']),
        'valor_raw' => $gift['valor'],
        'imagem' => $gift['imagem'] ?? null,
        'imagem_url' => getGiftImageUrl($gift['imagem'] ?? null),
        'status' => $gift['status'],
        'status_text' => $gift['status'] == 1 ? 'Comprado' : 'Disponível',
        'data_criacao' => formatDate($gift['data_criacao'], 'd/m/Y H:i')
    ];
}
?>
