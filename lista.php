<?php
require_once 'php/config.php';
require_once 'helpers/functions.php';
require_once 'functions/gifts_db.php';

// Configurações de paginação
$itemsPerPage = 12;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $itemsPerPage;

// Obter todos os presentes do banco
$searchTerm = $_GET['search'] ?? '';
$allGifts = getAllGifts();
$gifts = searchGifts($searchTerm);

// Calcular paginação
$totalItems = count($gifts);
$totalPages = ceil($totalItems / $itemsPerPage);

// Aplicar paginação aos resultados
$paginatedGifts = array_slice($gifts, $offset, $itemsPerPage);

// Formatar presentes para exibição
$formattedGifts = array_map('formatGiftForDisplay', $paginatedGifts);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presentes - <?php echo SITE_TITLE; ?></title>

    <!-- Favicon png-->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="pb-5">
    <!-- Background Image -->
    <div class="background-image"></div>

    <!-- Audio Player -->
    <audio id="backgroundMusic" loop preload="auto">
        <source src="assets/audio/musica.mp3" type="audio/mpeg">
        Seu navegador não suporta o elemento de áudio.
    </audio>

    <!-- Main Container -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-10 col-xl-8 mx-auto">
                <!-- Header -->
                <div class=" shadow-lg mb-4 rounded-4">
                    <div class="card-body text-center py-3">
                        <h1 class="couple-names mb-3">
                            <span class="name-1"><?php echo COUPLE_NAME_1; ?></span>
                            <span class="heart-icon mx-3">
                                <i class="fas fa-heart"></i>
                            </span>
                            <span class="name-2"><?php echo COUPLE_NAME_2; ?></span>
                        </h1>
                        <p class="wedding-date mb-4">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <?php echo WEDDING_DATE; ?>
                        </p>
                        <h2 class="mb-4 text-white">
                            <i class="fas fa-gift me-2"></i>
                            Lista de Presentes
                        </h2>

                        <!-- Controles -->
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <a href="index.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtro -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-search me-2"></i>
                            Filtrar Presentes
                        </h5>
                        <form method="GET" action="lista.php" class="row g-3 mt-3 align-items-center">
                            <div class="col-12 col-md-8 col-lg-<?php echo !empty($searchTerm) ? '7' : '9'; ?> mt-0 mb-3 mb-lg-0">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control"
                                        name="search"
                                        placeholder="Pesquise pelo nome..."
                                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-4 col-lg-<?php echo !empty($searchTerm) ? '5' : '3'; ?> mt-0">
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>
                                        Pesquisar
                                    </button>
                                    <?php if (!empty($searchTerm)): ?>
                                        <a href="lista.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>
                                            Limpar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <?php if (!empty($searchTerm)): ?>
                                    Mostrando <?php echo count($formattedGifts); ?> de <?php echo $totalItems; ?> presentes para "<?php echo htmlspecialchars($searchTerm); ?>"
                                <?php else: ?>
                                    Mostrando <?php echo count($formattedGifts); ?> de <?php echo $totalItems; ?> presentes
                                <?php endif; ?>
                                (Página <?php echo $currentPage; ?> de <?php echo $totalPages; ?>)
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Lista de Presentes -->
                <div class="card main-card shadow-lg">
                    <div class="card-body pb-4">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-list me-2"></i>
                            Presentes Disponíveis
                            <span class="badge bg-primary ms-2"><?php echo $totalItems; ?></span>
                        </h5>

                        <?php if (empty($gifts)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum presente encontrado</h5>
                                <p class="text-muted">
                                    <?php if (!empty($searchTerm)): ?>
                                        Tente ajustar o filtro de busca.
                                    <?php else: ?>
                                        Não há presentes cadastrados no momento.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($formattedGifts as $gift): ?>
                                    <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4">
                                        <div class="card gift-card h-100 ">
                                            <div class="card-body d-flex flex-column ">

                                                <!-- Imagem do Presente -->
                                                <div class="gift-image mb-0 text-center">
                                                    <i class="fas fa-gift fa-3x text-primary"></i>
                                                </div>

                                                <!-- Informações do Presente -->
                                                <h4 class="card-title text-center"><?php echo htmlspecialchars($gift['titulo']); ?></h4>

                                                <!-- Preço -->
                                                <div class="price-section mb-3">
                                                    <h5 class="text-primary mb-0 text-center fw-bold"><?php echo $gift['valor']; ?></h5>
                                                </div>

                                                <!-- Botão de Ação -->
                                                <div class="mt-auto">
                                                    <button class="btn btn-primary w-100 checkout-btn d-flex align-items-center justify-content-center gap-2"
                                                        data-gift-id="<?php echo $gift['id']; ?>"
                                                        data-gift-name="<?php echo htmlspecialchars($gift['titulo']); ?>"
                                                        data-gift-value="<?php echo $gift['valor']; ?>"
                                                        onclick="openCheckoutModal(this)">
                                                        <i class="fas fa-gift me-2"></i>
                                                        <span class="d-none d-md-block">Resgatar Presente</span>
                                                        <span class="d-block d-md-none">Resgatar</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Controles de Paginação -->
                        <?php if ($totalPages > 1): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Paginação de presentes">
                                    <ul class="pagination">
                                        <!-- Botão Anterior -->
                                        <?php if ($currentPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>" aria-label="Página anterior">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fas fa-chevron-left"></i>
                                                </span>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Páginas -->
                                        <?php
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $currentPage + 2);

                                        // Mostrar primeira página se não estiver no range
                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- Páginas do range -->
                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- Mostrar última página se não estiver no range -->
                                        <?php if ($endPage < $totalPages): ?>
                                            <?php if ($endPage < $totalPages - 1): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>"><?php echo $totalPages; ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Botão Próximo -->
                                        <?php if ($currentPage < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>" aria-label="Próxima página">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="notificationToast" class="toast" role="alert">
            <div class="toast-header">
                <i class="fas fa-info-circle text-primary me-2"></i>
                <strong class="me-auto">Notificação</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Mensagem aqui
            </div>
        </div>
    </div>

    <!-- Modal de Checkout PIX -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="checkoutModalLabel">
                        <i class="fas fa-gift me-2"></i>
                        Presentear com PIX
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Informações do Presente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted mb-2">Presente Selecionado</h6>
                                    <h4 class="card-text text-primary mb-1" id="checkoutGiftName">Nome do Presente</h4>
                                    <h3 class="card-text text-success fw-bold" id="checkoutGiftValue">R$ 0,00</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passo 1: Seus Dados -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Passo 1: Seus Dados
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="donorName" class="form-label">Seu Nome *</label>
                                        <input type="text" class="form-control" id="donorName" 
                                               placeholder="Informe seu nome" required>
                                        <small class="text-muted">Este nome aparecerá no histórico de presentes</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="donorPhone" class="form-label">WhatsApp (Opcional)</label>
                                        <input type="tel" class="form-control" id="donorPhone" 
                                               placeholder="(11) 99999-9999">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passo 2: Chave PIX -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-key me-2"></i>
                                        Passo 2: Chave PIX
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">
                                        Copie a chave PIX abaixo e faça o PIX com o valor de <strong id="checkoutGiftValueStep2">R$ 0,00</strong>
                                    </p>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-lg text-center fw-bold" 
                                               id="pixKeyDisplay" value="<?php echo PIX_KEY; ?>" readonly>
                                        <button class="btn btn-outline-primary" type="button" onclick="copyPixKey()">
                                            <i class="fas fa-copy me-1"></i>
                                            Copiar
                                        </button>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Esta é a chave PIX oficial da <?php echo PIX_OWNER_NAME; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passo 3: WhatsApp -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        Passo 3: Enviar Comprovante
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted mb-3">
                                        Após realizar o PIX, envie o comprovante para o WhatsApp da Marislan
                                    </p>
                                    <a id="whatsappLink" href="#" class="btn btn-success btn-lg" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        Enviar Comprovante pelo WhatsApp
                                    </a>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Clique no botão acima para abrir o WhatsApp com uma mensagem pré-formatada
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passo 4: Confirmar Envio -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Passo 4: Confirmar Envio
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted mb-3">
                                        Após enviar o comprovante pelo WhatsApp, confirme que o presente foi enviado
                                    </p>
                                    <button id="confirmGiftBtn" class="btn btn-warning btn-lg" onclick="confirmGift()">
                                        <i class="fas fa-gift me-2"></i>
                                        Confirmar Envio do Presente
                                    </button>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Esta ação salvará seu presente no sistema como "Pré Confirmado"
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agradecimento -->
                    <div class="alert alert-success text-center">
                        <h6 class="alert-heading">
                            <i class="fas fa-heart me-2"></i>
                            Muito Obrigado!
                        </h6>
                        <p class="mb-0">
                            Sua contribuição é muito especial para nós! 
                            <br>
                            <strong>Marislan e Douglas</strong>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    

    <!-- Custom JS -->
    <!-- PIX Functions JS -->
    <script src="assets/js/pix-functions.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/music-controller.js"></script>

    <!-- Configuração JavaScript -->
    <script>
        // Configuração global do projeto
        window.BASE_URL = '<?php echo BASE_URL; ?>';
    </script>

    <script>
        $(document).ready(function() {
            // Evento do botão resgatar presente (mantido para compatibilidade)
            $('.resgatar-btn').click(function() {
                const giftId = $(this).data('gift-id');
                const giftName = $(this).data('gift-name');

                // Por enquanto, apenas mostra um alerta
                // Depois implementaremos a funcionalidade completa
                showNotification('Funcionalidade em desenvolvimento', 'info');
            });
        });
    </script>
</body>

</html>