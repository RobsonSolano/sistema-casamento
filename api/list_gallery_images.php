<?php
/**
 * API para listar imagens da galeria
 */

header('Content-Type: application/json');

$galleryFolder = __DIR__ . '/../assets/images/galeria/';
$images = [];

// Verificar se a pasta existe
if (is_dir($galleryFolder)) {
    // Buscar todos os arquivos na pasta
    $files = scandir($galleryFolder);
    
    foreach ($files as $file) {
        // Filtrar apenas arquivos de imagem
        if ($file !== '.' && $file !== '..') {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            // Extensões de imagem permitidas
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // Extrair número entre parênteses
                preg_match('/\((\d+)\)/', $file, $matches);
                $orderNumber = isset($matches[1]) ? intval($matches[1]) : 9999;
                
                $images[] = [
                    'name' => $file,
                    'url' => 'assets/images/galeria/' . $file,
                    'order' => $orderNumber
                ];
            }
        }
    }
    
    // Ordenar por número entre parênteses
    usort($images, function($a, $b) {
        return $a['order'] - $b['order'];
    });
}

echo json_encode([
    'success' => true,
    'images' => $images,
    'count' => count($images)
]);
?>

