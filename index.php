<?php
require_once 'php/config.php';
require_once 'helpers/functions.php';
require_once 'functions/gifts_db.php';

// Buscar alguns presentes do banco para preview
$previewGifts = array_slice(getAllGifts(), 0, 3);
$formattedPreviewGifts = array_map('formatGiftForDisplay', $previewGifts);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>

    <!-- Favicon png-->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Carregamento da font Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>

<body class="">
    <!-- Background Image -->
    <div class="background-image"></div>

    <!-- Audio Player -->
    <audio id="backgroundMusic" loop preload="auto">
        <source src="assets/audio/musica.mp3" type="audio/mpeg">
        Seu navegador não suporta o elemento de áudio.
    </audio>


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content w-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-10 col-xl-6">
                        <div class="couple-initials mb-4 d-flex flex-column align-items-center justify-content-center gap-3">
                            <div class="initials-box mb-4">
                                <div class="feather-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="initials h1 mb-0 d-flex align-items-center justify-content-center gap-2">
                                    <span>M</span>
                                    <span style="margin-top: -30px;">.</span>
                                    <span>D</span>
                                </div>
                            </div>

                            <h1 class=" h1 couple-names mb-3 text-white d-flex align-items-center flex-column flex-md-row justify-content-center gap-2">
                                <span class="name-1">MARISLAN</span>
                                <span>&</span>
                                <span class="name-2">DOUGLAS</span>
                            </h1>

                            <!-- Wedding Date -->
                            <h2 class="h2 wedding-date mb-4  text-white">
                                13 | 12 | 2025
                            </h2>

                            <!-- Welcome Message -->
                            <p class="welcome-message mb-4 text-white text-center">
                                <!-- Mensagem carinhosa para todos os convidados que estão acessando a página -->
                                <span class="d-block mb-2" style="font-size: 1.1em; font-weight: 300;">
                                    Queridos familiares e amigos,
                                </span>
                                <span class="d-block mb-3" style="line-height: 1.6;">
                                    Este é um momento único em nossas vidas, onde dois corações se unem para formar uma só família.
                                    Será uma imensa alegria e honra ter você conosco neste dia tão especial,
                                    compartilhando conosco a celebração do nosso amor e da promessa de uma vida juntos.
                                </span>
                                <span class="d-block" style="font-style: italic; font-weight: 300;">
                                    Sua presença tornará este dia ainda mais inesquecível.
                                </span>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Countdown Section -->
    <section class="countdown-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="countdown-content text-center">
                        <h2 class="countdown-title">CONTAGEM REGRESSIVA</h2>
                        <div class="countdown-boxes">
                            <div class="countdown-box">
                                <div class="countdown-number" id="days">00</div>
                                <div class="countdown-label">DIAS</div>
                            </div>
                            <div class="countdown-box">
                                <div class="countdown-number" id="hours">00</div>
                                <div class="countdown-label">HORAS</div>
                            </div>
                            <div class="countdown-box">
                                <div class="countdown-number" id="minutes">00</div>
                                <div class="countdown-label">
                                    <span class="d-none d-md-block">MINUTOS</span>
                                    <span class="d-block d-md-none">MIN.</span>
                                </div>
                            </div>
                            <div class="countdown-box">
                                <div class="countdown-number" id="seconds">00</div>
                                <div class="countdown-label">
                                    <span class="d-none d-md-block">SEGUNDOS</span>
                                    <span class="d-block d-md-none">SEG.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gifts Preview Section -->
    <section class="gifts-section border-bottom pb-6">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="text-center mb-5">
                        <div class="branch-icon mb-3">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h2 class="section-title">LISTA DE PRESENTES</h2>
                        <p class="section-description">
                            Contribua para tornar nosso casamento ainda mais especial com um presente escolhido com carinho.
                        </p>
                    </div>

                    <!-- Sample Gifts -->
                    <?php if (!empty($formattedPreviewGifts)): ?>
                        <div class="row g-4 mb-5">
                            <?php foreach ($formattedPreviewGifts as $gift): ?>
                                <div class="col-6 col-sm-6 col-md-6 col-xl-4">
                                    <div class="gift-preview-card">
                                        <!-- Imagem do Presente -->
                                        <div class="gift-image mb-3 text-center">
                                            <i class="fas fa-gift fa-3x text-primary"></i>
                                        </div>

                                        <!-- Informações do Presente -->
                                        <h5 class="card-title text-center"><?php echo htmlspecialchars($gift['titulo']); ?></h5>

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
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum presente cadastrado</h5>
                            <p class="text-muted">Os presentes serão adicionados em breve.</p>
                        </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <button class="btn btn-primary btn-lg" id="viewAllGiftsBtn">
                            <i class="fas fa-gift me-2"></i>
                            Ver Todos os Presentes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de Boas-vindas -->
    <div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content welcome-modal-content">
                <div class="modal-body text-center p-5">
                    <div class="welcome-icon mb-4">
                        <i class="fas fa-heart fa-3x text-primary"></i>
                    </div>
                    <h3 class="modal-title mb-3" id="welcomeModalLabel">
                        Bem-vindos ao nosso casamento!
                    </h3>
                    <p class="mb-4 text-muted">
                        Estamos muito felizes em compartilhar este momento especial com vocês. <br>
                        Aproveitem cada instante desta celebração!
                    </p>
                    <button type="button" class="btn btn-primary btn-lg px-5" id="welcomeOkBtn">
                        <i class="fas fa-heart me-2"></i>
                        Continuar
                    </button>
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


    <!-- Seção Deixe Seu Recado -->
    <div class="container-fluid py-7 gifts-section">
        <div class="row px-3">
            <div class="col-12 col-md-10 col-lg-8 mx-auto">
                <div class="card main-card shadow-lg m-0">
                    <div class="card-body p-3 p-md-5">
                        <!-- Título da Seção -->
                        <div class="text-center mb-4">
                            <div class="branch-icon mb-3">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h2 class="section-title">DEIXE SEU RECADO</h2>
                        </div>

                        <!-- Formulário -->
                        <form id="guestbookForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="guestName" class="form-label">Seu Nome *</label>
                                    <input type="text" class="form-control" id="guestName" name="nome" placeholder="Digite seu nome" required>
                                </div>

                                <div class="col-12">
                                    <label for="guestMessage" class="form-label">Seu Recado *</label>
                                    <textarea class="form-control" maxlength="500" id="guestMessage" name="mensagem" rows="4" placeholder="Deixe aqui sua mensagem de carinho para o casal..." required></textarea>
                                    <div class="form-text">Mínimo 10 caracteres</div>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submitRecadoBtn">
                                        <i class="fas fa-heart me-2"></i>
                                        <span class="btn-text">Enviar Recado</span>
                                        <span class="btn-loading d-none">
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            Enviando...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
    
    

    <!-- Configuração JavaScript -->
    <script>
        // Configuração global do projeto
        window.BASE_URL = '<?php echo BASE_URL; ?>';
    </script>

    <!-- PIX Functions JS -->
    <script src="assets/js/pix-functions.js"></script>
    
    <!-- Music Controller JS -->
    <script src="assets/js/music-controller.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>

</html>