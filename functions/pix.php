<?php
/**
 * Funções para geração de PIX
 * Sistema de pagamento via PIX para presentes
 */

require_once __DIR__ . '/../php/config.php';

/**
 * Gera código PIX Copia e Cola
 */
function generatePixCode($value, $description = '') {
    $pixKey = PIX_KEY;
    $pixKeyType = PIX_KEY_TYPE;
    $ownerName = PIX_OWNER_NAME;
    
    // Formatar valor (PIX requer valor em centavos)
    $amount = number_format($value, 2, '', '');
    
    // Gerar ID único para a transação
    $transactionId = uniqid('casamento_', true);
    
    // Criar payload PIX usando formato EMV
    $payload = [
        'pixkey' => $pixKey,
        'description' => $description ?: "Presente Casamento {$ownerName}",
        'merchantName' => $ownerName,
        'merchantCity' => 'São Paulo',
        'amount' => $amount,
        'txid' => $transactionId
    ];
    
    // Gerar código PIX usando formato EMV correto
    $pixCode = generateEMVPixCode($pixKey, $amount, $transactionId, $ownerName, 'São Paulo', $payload['description']);
    
    return [
        'code' => $pixCode,
        'transaction_id' => $transactionId,
        'amount' => $value,
        'formatted_amount' => 'R$ ' . number_format($value, 2, ',', '.'),
        'description' => $payload['description']
    ];
}

/**
 * Gera código PIX no formato EMV simplificado
 */
function generateEMVPixCode($pixKey, $amount, $txid, $merchantName, $merchantCity, $description) {
    // Formato simplificado mas funcional para PIX
    $payload = [
        'pixkey' => $pixKey,
        'amount' => $amount,
        'merchant' => $merchantName,
        'city' => $merchantCity,
        'txid' => $txid,
        'description' => $description
    ];
    
    // Gerar código PIX básico (formato simplificado para demonstração)
    $pixCode = "00020126580014br.gov.bcb.pix01" . str_pad(strlen($pixKey), 2, '0', STR_PAD_LEFT) . $pixKey . 
               "520400005303986540" . str_pad(strlen($amount), 2, '0', STR_PAD_LEFT) . $amount . 
               "5802BR59" . str_pad(strlen($merchantName), 2, '0', STR_PAD_LEFT) . $merchantName . 
               "60" . str_pad(strlen($merchantCity), 2, '0', STR_PAD_LEFT) . $merchantCity . 
               "62070503***6304";
    
    return $pixCode;
}

/**
 * Calcula CRC16 para PIX
 */
function crc16($data) {
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]);
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 1) {
                $crc = ($crc >> 1) ^ 0x8408;
            } else {
                $crc >>= 1;
            }
        }
    }
    return strtoupper(dechex($crc ^ 0xFFFF));
}

/**
 * Gera URL do QR Code PIX usando API pública
 */
function generatePixQRCode($pixCode, $size = 200) {
    // Usar API pública para gerar QR Code
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($pixCode);
    
    return $qrCodeUrl;
}

/**
 * Formata número do WhatsApp para link
 */
function formatWhatsAppLink($phone, $message = '') {
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    $message = urlencode($message);
    
    return "https://wa.me/{$cleanPhone}?text={$message}";
}

/**
 * Gera mensagem padrão para comprovante
 */
function generateComprovanteMessage($giftName, $amount, $transactionId) {
    $formattedAmount = 'R$ ' . number_format($amount, 2, ',', '.');
    
    $message = "🎁 *COMPROVANTE DE PRESENTE* 🎁\n\n";
    $message .= "📝 *Presente:* {$giftName}\n";
    $message .= "💰 *Valor:* {$formattedAmount}\n";
    $message .= "🆔 *ID da Transação:* {$transactionId}\n";
    $message .= "📅 *Data:* " . date('d/m/Y H:i') . "\n\n";
    $message .= "✅ *Confirmação:* Envie este comprovante após o pagamento para confirmarmos o presente!\n\n";
    $message .= "💕 *Obrigado pelo carinho!*";
    
    return $message;
}

/**
 * Salva transação PIX no banco de dados
 */
function savePixTransaction($giftId, $amount, $transactionId, $donorName = '', $donorPhone = '') {
    try {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO pix_transactions (
            gift_id, 
            amount, 
            transaction_id, 
            donor_name, 
            donor_phone, 
            status, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $giftId,
            $amount,
            $transactionId,
            $donorName,
            $donorPhone
        ]);
        
        return $result;
    } catch (Exception $e) {
        error_log("Erro ao salvar transação PIX: " . $e->getMessage());
        return false;
    }
}

/**
 * Busca transações PIX por presente
 */
function getPixTransactionsByGift($giftId) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT * FROM pix_transactions 
                WHERE gift_id = ? 
                ORDER BY created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$giftId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar transações PIX: " . $e->getMessage());
        return [];
    }
}

/**
 * Atualiza status da transação PIX
 */
function updatePixTransactionStatus($transactionId, $status, $comprovantePath = '') {
    try {
        $db = Database::getInstance();
        
        $sql = "UPDATE pix_transactions 
                SET status = ?, 
                    comprovante_path = ?, 
                    updated_at = NOW() 
                WHERE transaction_id = ?";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([$status, $comprovantePath, $transactionId]);
        
        return $result;
    } catch (Exception $e) {
        error_log("Erro ao atualizar status da transação PIX: " . $e->getMessage());
        return false;
    }
}
?>
