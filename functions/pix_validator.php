<?php
/**
 * Gerador de PIX válido seguindo o padrão EMV do Banco Central
 * Implementação própria baseada nas especificações oficiais
 */

class PixValidator {
    
    /**
     * Gera código PIX válido seguindo o padrão EMV
     */
    public static function generatePixCode($pixKey, $amount, $merchantName, $merchantCity, $txid, $description = '') {
        // Converter valor para centavos
        $amountInCents = (int) round($amount * 100);
        
        // Payload Format Indicator
        $payloadFormatIndicator = '000201';
        
        // Point of Initiation Method (02 = estático)
        $pointOfInitiationMethod = '0102';
        
        // Merchant Account Information
        $pixKeyLength = strlen($pixKey);
        $merchantAccountInfo = '26' . 
            str_pad(14 + $pixKeyLength, 2, '0', STR_PAD_LEFT) . 
            '0014br.gov.bcb.pix01' . 
            str_pad($pixKeyLength, 2, '0', STR_PAD_LEFT) . 
            $pixKey;
        
        // Merchant Category Code
        $merchantCategoryCode = '520400005303986';
        
        // Transaction Currency (986 = BRL)
        $transactionCurrency = '540' . str_pad(strlen($amountInCents), 2, '0', STR_PAD_LEFT) . $amountInCents;
        
        // Country Code
        $countryCode = '5802BR';
        
        // Merchant Name
        $merchantNameLength = strlen($merchantName);
        $merchantNameField = '59' . str_pad($merchantNameLength, 2, '0', STR_PAD_LEFT) . $merchantName;
        
        // Merchant City
        $merchantCityLength = strlen($merchantCity);
        $merchantCityField = '60' . str_pad($merchantCityLength, 2, '0', STR_PAD_LEFT) . $merchantCity;
        
        // Additional Data Field Template
        $txidLength = strlen($txid);
        $additionalDataField = '62' . 
            str_pad(5 + $txidLength, 2, '0', STR_PAD_LEFT) . 
            '05' . 
            str_pad($txidLength, 2, '0', STR_PAD_LEFT) . 
            $txid;
        
        // Concatenar todos os campos
        $data = $payloadFormatIndicator . 
                $pointOfInitiationMethod . 
                $merchantAccountInfo . 
                $merchantCategoryCode . 
                $transactionCurrency . 
                $countryCode . 
                $merchantNameField . 
                $merchantCityField . 
                $additionalDataField . 
                '6304';
        
        // Calcular CRC16
        $crc = self::calculateCRC16($data);
        
        return $data . $crc;
    }
    
    /**
     * Calcula CRC16 para PIX
     */
    private static function calculateCRC16($data) {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc <<= 1;
                }
            }
        }
        
        return strtoupper(dechex($crc ^ 0xFFFF));
    }
    
    /**
     * Valida se o código PIX está correto
     */
    public static function validatePixCode($pixCode) {
        if (strlen($pixCode) < 50) {
            return false;
        }
        
        if (!preg_match('/^000201/', $pixCode)) {
            return false;
        }
        
        if (!preg_match('/6304[A-F0-9]{4}$/', $pixCode)) {
            return false;
        }
        
        // Verificar CRC16
        $data = substr($pixCode, 0, -4);
        $crc = substr($pixCode, -4);
        $calculatedCrc = self::calculateCRC16($data);
        
        return $crc === $calculatedCrc;
    }
    
    /**
     * Gera QR Code usando API externa
     */
    public static function generateQRCode($pixCode, $size = 200) {
        $encodedPixCode = urlencode($pixCode);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedPixCode}";
    }
    
    /**
     * Gera QR Code alternativo usando Google Charts
     */
    public static function generateQRCodeGoogle($pixCode, $size = 200) {
        $encodedPixCode = urlencode($pixCode);
        return "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$encodedPixCode}";
    }
    
    /**
     * Salva transação PIX no banco
     */
    public static function savePixTransaction($giftId, $giftName, $amount, $pixCode, $donorName = '', $donorPhone = '') {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO pix_transactions 
                (gift_id, gift_name, amount, pix_code, donor_name, donor_phone, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            return $stmt->execute([
                $giftId,
                $giftName,
                $amount,
                $pixCode,
                $donorName,
                $donorPhone
            ]);
        } catch (Exception $e) {
            error_log("Erro ao salvar transação PIX: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Busca transações PIX
     */
    public static function getPixTransactions($search = '', $status = '') {
        global $pdo;
        
        $sql = "SELECT * FROM pix_transactions WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (gift_name LIKE ? OR donor_name LIKE ? OR pix_code LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar transações PIX: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Atualiza status da transação
     */
    public static function updateTransactionStatus($transactionId, $status) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("UPDATE pix_transactions SET status = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$status, $transactionId]);
        } catch (Exception $e) {
            error_log("Erro ao atualizar status da transação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calcula estatísticas das transações
     */
    public static function calculatePixStats() {
        global $pdo;
        
        try {
            $stmt = $pdo->query("
                SELECT 
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status IN ('paid', 'confirmed') THEN amount ELSE 0 END) as total_amount
                FROM pix_transactions
            ");
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao calcular estatísticas PIX: " . $e->getMessage());
            return [
                'total_transactions' => 0,
                'pending' => 0,
                'paid' => 0,
                'confirmed' => 0,
                'cancelled' => 0,
                'total_amount' => 0
            ];
        }
    }
}
?>
