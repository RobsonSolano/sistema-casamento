<?php
/**
 * Funções relacionadas aos presentes
 * Arquivo com funções específicas para gerenciamento de presentes
 */

require_once __DIR__ . '/../helpers/functions.php';

/**
 * Estrutura de dados para presentes
 * @return array
 */
function getGiftsStructure() {
    return [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'price' => 'float',
        'category' => 'string',
        'image' => 'string',
        'purchased' => 'boolean',
        'purchased_by' => 'string',
        'purchased_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}

/**
 * Lista de categorias de presentes
 * @return array
 */
function getGiftCategories() {
    return [
        'casa' => 'Casa e Decoração',
        'cozinha' => 'Cozinha',
        'quarto' => 'Quarto',
        'banheiro' => 'Banheiro',
        'eletronicos' => 'Eletrônicos',
        'roupas' => 'Roupas',
        'livros' => 'Livros',
        'viagem' => 'Viagem',
        'outros' => 'Outros'
    ];
}

/**
 * Exemplo de lista de presentes
 * @return array
 */
function getSampleGifts() {
    return [
        [
            'id' => 1,
            'name' => 'Jogo de Pratos',
            'description' => 'Jogo completo de pratos para 12 pessoas',
            'price' => 299.90,
            'category' => 'cozinha',
            'image' => 'assets/images/gifts/pratos.jpg',
            'purchased' => false,
            'purchased_by' => '',
            'purchased_date' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'name' => 'Aspirador de Pó',
            'description' => 'Aspirador de pó potente para limpeza da casa',
            'price' => 450.00,
            'category' => 'casa',
            'image' => 'assets/images/gifts/aspirador.jpg',
            'purchased' => false,
            'purchased_by' => '',
            'purchased_date' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 3,
            'name' => 'Cama King Size',
            'description' => 'Cama king size com colchão de molas',
            'price' => 1200.00,
            'category' => 'quarto',
            'image' => 'assets/images/gifts/cama.jpg',
            'purchased' => true,
            'purchased_by' => 'Tia Maria',
            'purchased_date' => '2024-11-15 10:30:00',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 4,
            'name' => 'Smart TV 55"',
            'description' => 'Smart TV 55 polegadas 4K',
            'price' => 2500.00,
            'category' => 'eletronicos',
            'image' => 'assets/images/gifts/tv.jpg',
            'purchased' => false,
            'purchased_by' => '',
            'purchased_date' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 5,
            'name' => 'Viagem para Paris',
            'description' => 'Pacote de viagem para Paris para 2 pessoas',
            'price' => 8000.00,
            'category' => 'viagem',
            'image' => 'assets/images/gifts/paris.jpg',
            'purchased' => false,
            'purchased_by' => '',
            'purchased_date' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ];
}

/**
 * Filtra presentes por categoria
 * @param array $gifts
 * @param string $category
 * @return array
 */
function filterGiftsByCategory($gifts, $category) {
    if (empty($category)) {
        return $gifts;
    }
    
    return array_filter($gifts, function($gift) use ($category) {
        return $gift['category'] === $category;
    });
}

/**
 * Filtra presentes por status (comprado/não comprado)
 * @param array $gifts
 * @param bool $purchased
 * @return array
 */
function filterGiftsByStatus($gifts, $purchased = false) {
    return array_filter($gifts, function($gift) use ($purchased) {
        return $gift['purchased'] === $purchased;
    });
}

/**
 * Ordena presentes por preço
 * @param array $gifts
 * @param string $order
 * @return array
 */
function sortGiftsByPrice($gifts, $order = 'asc') {
    usort($gifts, function($a, $b) use ($order) {
        if ($order === 'desc') {
            return $b['price'] <=> $a['price'];
        }
        return $a['price'] <=> $b['price'];
    });
    
    return $gifts;
}

/**
 * Busca presentes por termo
 * @param array $gifts
 * @param string $searchTerm
 * @return array
 */
function searchGifts($gifts, $searchTerm) {
    if (empty($searchTerm)) {
        return $gifts;
    }
    
    $searchTerm = strtolower($searchTerm);
    
    return array_filter($gifts, function($gift) use ($searchTerm) {
        return strpos(strtolower($gift['name']), $searchTerm) !== false ||
               strpos(strtolower($gift['description']), $searchTerm) !== false;
    });
}

/**
 * Calcula estatísticas dos presentes
 * @param array $gifts
 * @return array
 */
function calculateGiftStats($gifts) {
    $totalGifts = count($gifts);
    $purchasedGifts = count(filterGiftsByStatus($gifts, true));
    $availableGifts = $totalGifts - $purchasedGifts;
    
    $totalValue = array_sum(array_column($gifts, 'price'));
    $purchasedValue = array_sum(array_column(filterGiftsByStatus($gifts, true), 'price'));
    $availableValue = $totalValue - $purchasedValue;
    
    return [
        'total_gifts' => $totalGifts,
        'purchased_gifts' => $purchasedGifts,
        'available_gifts' => $availableGifts,
        'total_value' => $totalValue,
        'purchased_value' => $purchasedValue,
        'available_value' => $availableValue,
        'purchase_percentage' => $totalGifts > 0 ? round(($purchasedGifts / $totalGifts) * 100, 2) : 0
    ];
}

/**
 * Valida dados de presente
 * @param array $data
 * @return array
 */
function validateGiftData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Nome do presente é obrigatório';
    }
    
    if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
        $errors[] = 'Preço deve ser um valor numérico positivo';
    }
    
    if (!empty($data['category']) && !array_key_exists($data['category'], getGiftCategories())) {
        $errors[] = 'Categoria inválida';
    }
    
    return $errors;
}

/**
 * Formata presente para exibição
 * @param array $gift
 * @return array
 */
function formatGiftForDisplay($gift) {
    $categories = getGiftCategories();
    
    return [
        'id' => $gift['id'],
        'name' => $gift['name'],
        'description' => $gift['description'],
        'price' => formatCurrency($gift['price']),
        'price_raw' => $gift['price'],
        'category' => $categories[$gift['category']] ?? 'Outros',
        'category_key' => $gift['category'],
        'image' => $gift['image'],
        'purchased' => $gift['purchased'],
        'purchased_by' => $gift['purchased_by'],
        'purchased_date' => $gift['purchased_date'] ? formatDate($gift['purchased_date'], 'd/m/Y H:i') : null,
        'created_at' => formatDate($gift['created_at'], 'd/m/Y H:i'),
        'updated_at' => formatDate($gift['updated_at'], 'd/m/Y H:i')
    ];
}

/**
 * Simula compra de presente
 * @param int $giftId
 * @param string $purchasedBy
 * @return array
 */
function purchaseGift($giftId, $purchasedBy) {
    $gifts = getSampleGifts();
    
    foreach ($gifts as &$gift) {
        if ($gift['id'] == $giftId) {
            if ($gift['purchased']) {
                return [
                    'success' => false,
                    'message' => 'Este presente já foi comprado'
                ];
            }
            
            $gift['purchased'] = true;
            $gift['purchased_by'] = $purchasedBy;
            $gift['purchased_date'] = date('Y-m-d H:i:s');
            $gift['updated_at'] = date('Y-m-d H:i:s');
            
            return [
                'success' => true,
                'message' => 'Presente comprado com sucesso!',
                'gift' => $gift
            ];
        }
    }
    
    return [
        'success' => false,
        'message' => 'Presente não encontrado'
    ];
}

/**
 * Gera relatório de presentes
 * @param array $gifts
 * @return array
 */
function generateGiftReport($gifts) {
    $stats = calculateGiftStats($gifts);
    $categories = getGiftCategories();
    $categoryStats = [];
    
    foreach ($categories as $key => $name) {
        $categoryGifts = filterGiftsByCategory($gifts, $key);
        $categoryStats[$key] = [
            'name' => $name,
            'total' => count($categoryGifts),
            'purchased' => count(filterGiftsByStatus($categoryGifts, true)),
            'available' => count(filterGiftsByStatus($categoryGifts, false)),
            'total_value' => array_sum(array_column($categoryGifts, 'price')),
            'purchased_value' => array_sum(array_column(filterGiftsByStatus($categoryGifts, true), 'price'))
        ];
    }
    
    return [
        'stats' => $stats,
        'categories' => $categoryStats,
        'generated_at' => date('Y-m-d H:i:s')
    ];
}
?>
