<?php
/**
 * Debug da API PIX
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug da API PIX</h2>";

try {
    echo "<h3>1. Testando includes</h3>";
    
    require_once 'php/config.php';
    echo "✅ config.php carregado<br>";
    
    require_once 'functions/pix_real.php';
    echo "✅ pix_real.php carregado<br>";
    
    require_once 'functions/gifts_db.php';
    echo "✅ gifts_db.php carregado<br>";
    
    echo "<h3>2. Testando configurações</h3>";
    echo "PIX_KEY: " . PIX_KEY . "<br>";
    echo "PIX_OWNER_NAME: " . PIX_OWNER_NAME . "<br>";
    echo "WHATSAPP_NUMBER: " . WHATSAPP_NUMBER . "<br>";
    
    echo "<h3>3. Testando conexão com banco</h3>";
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com banco OK<br>";
    
    echo "<h3>4. Testando presente</h3>";
    $gift = getGiftById(1);
    if ($gift) {
        echo "✅ Presente encontrado: " . $gift['titulo'] . "<br>";
    } else {
        echo "❌ Presente não encontrado<br>";
    }
    
    echo "<h3>5. Testando geração PIX</h3>";
    $txid = 'casamento_' . time() . '_1';
    $pixCode = PixReal::generateRealPix(
        PIX_KEY,
        50.00,
        PIX_OWNER_NAME,
        'São Paulo',
        $txid,
        "Teste de PIX"
    );
    
    if ($pixCode) {
        echo "✅ PIX gerado: " . substr($pixCode, 0, 50) . "...<br>";
        
        if (PixReal::validatePixCode($pixCode)) {
            echo "✅ PIX válido<br>";
        } else {
            echo "❌ PIX inválido<br>";
        }
    } else {
        echo "❌ Erro ao gerar PIX<br>";
    }
    
    echo "<h3>6. Testando QR Code</h3>";
    $qrUrl = PixReal::generateQRCode($pixCode);
    echo "✅ QR Code URL: <a href='$qrUrl' target='_blank'>Ver QR Code</a><br>";
    
    echo "<h3>7. Testando salvamento</h3>";
    $saved = PixReal::savePixTransaction(1, 'Teste', 50.00, $pixCode, 'Teste', '11999999999');
    if ($saved) {
        echo "✅ Transação salva<br>";
    } else {
        echo "❌ Erro ao salvar transação<br>";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "<br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";
    echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
