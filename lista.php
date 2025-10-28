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
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="pt-5">
    <!-- Background Image -->
    <div class="background-image"></div>

    <!-- Audio Player -->
    <audio id="backgroundMusic" loop preload="auto">
        <source src="assets/audio/musica.mp3" type="audio/mpeg">
        Seu navegador não suporta o elemento de áudio.
    </audio>

    <!-- Volume Control Flutuante -->
    <div class="volume-control-floating" id="volumeControlFloating">
        <button class="volume-toggle-btn" id="volumeToggleBtn" title="Controle de Volume">
            <i class="fas fa-volume-up"></i>
        </button>
        <div class="volume-slider-container " id="volumeSliderContainer">
            <div class="d-flex gap-2 align-items-center">
                <input type="range" class="volume-slider" id="volumeSlider" min="0" max="100" value="40">
                <div class="volume-value" id="volumeValue">40%</div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-10 col-xl-8 mx-auto">
                <!-- Header -->
                <div class=" shadow-lg mb-4 rounded-4">
                    <div class="card-body text-center pt-4 pb-2">
                        <ha class="h4 couple-signature principal mt-3" style="font-size: 2rem !important">Marislan <br class="d-block d-md-none">& <br class="d-block d-md-none">Douglas</ha>
                        <h4 class="mb-4 text-white mt-3">
                            <i class="fas fa-gift me-2"></i>
                            Lista de Presentes
                        </h4>

                        <!-- Controles -->
                        <div class="d-flex justify-content-center gap-3 mb-3 ">
                            <a href="index.php" class="btn btn-info btn-sm px-4 py-2 text-white">
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
                                                <div class="gift-image <?php echo !empty($gift['imagem_url']) ? 'mb-3' : 'mb-0'; ?> text-center">
                                                    <?php if (!empty($gift['imagem_url'])): ?>
                                                        <img src="<?php echo $gift['imagem_url']; ?>"
                                                            alt="<?php echo htmlspecialchars($gift['titulo']); ?>"
                                                            class="gift-product-image">
                                                    <?php else: ?>
                                                        <i class="fas fa-gift fa-3x text-primary"></i>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Informações do Presente -->
                                                <h4 class="card-title text-center"><?php echo htmlspecialchars($gift['titulo']); ?></h4>

                                                <!-- Preço -->
                                                <div class="price-section mb-3">
                                                    <h5 class="text-primary mb-0 text-center fw-bold"><?php echo $gift['valor']; ?></h5>
                                                </div>

                                                <!-- Botão de Ação -->
                                                <div class="mt-auto d-flex justify-content-center">
                                                    <button class="btn btn-primary w-75 checkout-btn d-flex align-items-center justify-content-center gap-2"
                                                        data-gift-id="<?php echo $gift['id']; ?>"
                                                        data-gift-name="<?php echo htmlspecialchars($gift['titulo']); ?>"
                                                        data-gift-value="<?php echo $gift['valor']; ?>"
                                                        onclick="redirectToCheckout(this, '<?php echo $_SERVER['REQUEST_URI']; ?>')">
                                                        <i class="fas fa-gift me-2"></i>
                                                        <span class="d-none d-md-block">Comprar</span>
                                                        <span class="d-block d-md-none">Comprar</span>
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
            // Evento do botão Comprar (mantido para compatibilidade)
            $('.resgatar-btn').click(function() {
                const giftId = $(this).data('gift-id');
                const giftName = $(this).data('gift-name');

                // Por enquanto, apenas mostra um alerta
                // Depois implementaremos a funcionalidade completa
                showNotification('Funcionalidade em desenvolvimento', 'info');
            });
            
            // Controle de Volume Flutuante
            const musicElement = document.getElementById('backgroundMusic');
            const volumeSlider = document.getElementById('volumeSlider');
            const volumeValue = document.getElementById('volumeValue');
            const volumeToggleBtn = document.getElementById('volumeToggleBtn');
            
            if (musicElement && volumeSlider && volumeValue) {
                // Atualizar volume quando slider mudar
                volumeSlider.addEventListener('input', function() {
                    const volume = this.value / 100;
                    musicElement.volume = volume;
                    volumeValue.textContent = this.value + '%';
                    
                    // Atualizar ícone baseado no volume
                    updateVolumeIcon(this.value);
                });
                
                // Função para atualizar ícone do botão
                function updateVolumeIcon(volume) {
                    const icon = volumeToggleBtn.querySelector('i');
                    if (volume == 0) {
                        icon.className = 'fas fa-volume-mute';
                    } else if (volume < 30) {
                        icon.className = 'fas fa-volume-down';
                    } else {
                        icon.className = 'fas fa-volume-up';
                    }
                }
                
                // Botão toggle mute/unmute
                volumeToggleBtn.addEventListener('click', function() {
                    if (musicElement.muted || musicElement.volume === 0) {
                        musicElement.muted = false;
                        musicElement.volume = 0.4;
                        volumeSlider.value = 40;
                        volumeValue.textContent = '40%';
                        updateVolumeIcon(40);
                    } else {
                        musicElement.muted = true;
                        updateVolumeIcon(0);
                    }
                });
                
                // Inicializar ícone
                updateVolumeIcon(40);
            }
        });
    </script>
</body>

</html>