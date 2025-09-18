<?php
/**
 * Funções para gerenciar recados no banco de dados
 */

require_once __DIR__ . '/../php/Database.php';

/**
 * Busca todos os recados
 */
function getAllRecados() {
    $db = Database::getInstance();
    $sql = "SELECT * FROM recados ORDER BY data_envio DESC";
    return $db->fetchAll($sql);
}

/**
 * Busca recado por ID
 */
function getRecadoById($id) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM recados WHERE id = ?";
    return $db->fetchOne($sql, [$id]);
}

/**
 * Adiciona novo recado
 */
function addRecado($nome, $mensagem, $ipAddress = null, $userAgent = null) {
    $db = Database::getInstance();
    $sql = "INSERT INTO recados (nome, mensagem, ip_address, user_agent) VALUES (?, ?, ?, ?)";
    return $db->insert($sql, [$nome, $mensagem, $ipAddress, $userAgent]);
}

/**
 * Remove recado
 */
function deleteRecado($id) {
    $db = Database::getInstance();
    $sql = "DELETE FROM recados WHERE id = ?";
    return $db->delete($sql, [$id]);
}

/**
 * Busca recados com filtros
 */
function searchRecados($search = '') {
    $db = Database::getInstance();
    $sql = "SELECT * FROM recados WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (nome LIKE ? OR mensagem LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    
    $sql .= " ORDER BY data_envio DESC";
    
    return $db->fetchAll($sql, $params);
}

/**
 * Calcula estatísticas dos recados
 */
function calculateRecadosStats() {
    $db = Database::getInstance();
    
    // Total de recados
    $totalSql = "SELECT COUNT(*) as total FROM recados";
    $total = $db->fetchOne($totalSql)['total'];
    
    // Recados hoje
    $todaySql = "SELECT COUNT(*) as today FROM recados WHERE DATE(data_envio) = CURDATE()";
    $today = $db->fetchOne($todaySql)['today'];
    
    // Recados esta semana
    $weekSql = "SELECT COUNT(*) as week FROM recados WHERE data_envio >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $week = $db->fetchOne($weekSql)['week'];
    
    // Recados este mês
    $monthSql = "SELECT COUNT(*) as month FROM recados WHERE MONTH(data_envio) = MONTH(NOW()) AND YEAR(data_envio) = YEAR(NOW())";
    $month = $db->fetchOne($monthSql)['month'];
    
    return [
        'total_recados' => (int)$total,
        'today_recados' => (int)$today,
        'week_recados' => (int)$week,
        'month_recados' => (int)$month
    ];
}

/**
 * Valida dados do recado
 */
function validateRecadoData($nome, $mensagem) {
    $errors = [];
    
    if (empty($nome)) {
        $errors[] = 'Nome é obrigatório';
    } elseif (strlen($nome) < 2) {
        $errors[] = 'Nome deve ter pelo menos 2 caracteres';
    } elseif (strlen($nome) > 255) {
        $errors[] = 'Nome deve ter no máximo 255 caracteres';
    }
    
    if (empty($mensagem)) {
        $errors[] = 'Mensagem é obrigatória';
    } elseif (strlen($mensagem) < 10) {
        $errors[] = 'Mensagem deve ter pelo menos 10 caracteres';
    } elseif (strlen($mensagem) > 2000) {
        $errors[] = 'Mensagem deve ter no máximo 2000 caracteres';
    }
    
    return $errors;
}

/**
 * Formata recado para exibição
 */
function formatRecadoForDisplay($recado) {
    return [
        'id' => $recado['id'],
        'nome' => htmlspecialchars($recado['nome']),
        'mensagem' => nl2br(htmlspecialchars($recado['mensagem'])),
        'data_envio' => formatDate($recado['data_envio'], 'd/m/Y H:i'),
        'data_envio_full' => formatDate($recado['data_envio'], 'd/m/Y H:i:s'),
        'ip_address' => $recado['ip_address'],
        'user_agent' => $recado['user_agent']
    ];
}
?>
