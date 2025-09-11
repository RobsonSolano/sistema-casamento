<?php
// Versão simplificada do dashboard para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../php/config.php';
require_once '../helpers/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

echo "Dashboard carregado com sucesso!<br>";
echo "Usuário logado: " . ($_SESSION['admin_email'] ?? 'N/A') . "<br>";
echo "Sessão ativa: " . (isset($_SESSION['admin_logged_in']) ? 'Sim' : 'Não') . "<br>";

// Testar carregamento das funções de presentes
try {
    require_once '../functions/gifts.php';
    echo "Funções de presentes carregadas!<br>";
    
    $gifts = getSampleGifts();
    echo "Total de presentes: " . count($gifts) . "<br>";
    
} catch (Exception $e) {
    echo "Erro ao carregar funções: " . $e->getMessage() . "<br>";
}
?>
