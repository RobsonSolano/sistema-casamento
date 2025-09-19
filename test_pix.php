<?php
/**
 * Teste da API PIX
 */

require_once 'php/config.php';
require_once 'functions/pix_validator.php';

echo "<h2>Teste da API PIX</h2>";

// Teste 1: Verificar conexão com banco
echo "<h3>1. Teste de Conexão com Banco</h3>";
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com banco OK<br>";
    
    // Verificar se tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'pix_transactions'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabela 'pix_transactions' existe<br>";
    } else {
        echo "❌ Tabela 'pix_transactions' NÃO existe<br>";
        echo "Execute o arquivo create_pix_table.sql no banco<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "<br>";
}

// Teste 2: Verificar configurações PIX
echo "<h3>2. Configurações PIX</h3>";
echo "PIX_KEY: " . (defined('PIX_KEY') ? PIX_KEY : 'NÃO DEFINIDA') . "<br>";
echo "PIX_OWNER_NAME: " . (defined('PIX_OWNER_NAME') ? PIX_OWNER_NAME : 'NÃO DEFINIDO') . "<br>";
echo "WHATSAPP_NUMBER: " . (defined('WHATSAPP_NUMBER') ? WHATSAPP_NUMBER : 'NÃO DEFINIDO') . "<br>";

// Teste 3: Gerar código PIX
echo "<h3>3. Teste de Geração PIX</h3>";
try {
    $pixCode = PixValidator::generatePixCode(
        PIX_KEY,
        50.00,
        PIX_OWNER_NAME,
        'São Paulo',
        'teste_' . time(),
        'Teste de PIX'
    );
    
    if ($pixCode) {
        echo "✅ Código PIX gerado: " . substr($pixCode, 0, 50) . "...<br>";
        
        // Validar código
        if (PixValidator::validatePixCode($pixCode)) {
            echo "✅ Código PIX válido<br>";
        } else {
            echo "❌ Código PIX inválido<br>";
        }
        
        // Gerar QR Code
        $qrUrl = PixValidator::generateQRCode($pixCode);
        echo "✅ QR Code URL: <a href='$qrUrl' target='_blank'>Ver QR Code</a><br>";
        
    } else {
        echo "❌ Erro ao gerar código PIX<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 4: Simular chamada da API
echo "<h3>4. Teste da API</h3>";
try {
    $testData = [
        'gift_id' => 1,
        'amount' => 50.00,
        'donor_name' => 'Teste',
        'donor_phone' => '11999999999'
    ];
    
    // Simular processamento da API
    $giftId = $testData['gift_id'];
    $amount = $testData['amount'];
    $donorName = $testData['donor_name'];
    $donorPhone = $testData['donor_phone'];
    
    // Buscar presente
    require_once 'functions/gifts_db.php';
    $gift = getGiftById($giftId);
    
    if ($gift) {
        echo "✅ Presente encontrado: " . $gift['titulo'] . "<br>";
        
        // Gerar PIX
        $txid = 'casamento_' . time() . '_' . $giftId;
        $pixCode = PixValidator::generatePixCode(
            PIX_KEY,
            $amount,
            PIX_OWNER_NAME,
            'São Paulo',
            $txid,
            "Presente: {$gift['titulo']}"
        );
        
        if ($pixCode && PixValidator::validatePixCode($pixCode)) {
            echo "✅ PIX gerado e validado com sucesso<br>";
            echo "📋 Código completo: " . $pixCode . "<br>";
        } else {
            echo "❌ Erro na geração/validação do PIX<br>";
        }
        
    } else {
        echo "❌ Presente não encontrado<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na API: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>Próximos passos:</strong></p>";
echo "<ol>";
echo "<li>Se a tabela não existe, execute create_pix_table.sql</li>";
echo "<li>Verifique se as configurações PIX estão corretas</li>";
echo "<li>Teste a geração de PIX</li>";
echo "</ol>";
?>
