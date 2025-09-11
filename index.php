<?php
require_once 'php/config.php';
require_once 'helpers/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Background Image -->
    <div class="background-image"></div>
    
    <!-- Audio Player -->
    <audio id="backgroundMusic" loop preload="auto">
        <source src="assets/audio/musica.mp3" type="audio/mpeg">
        Seu navegador não suporta o elemento de áudio.
    </audio>
    
    <!-- Main Container -->
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 d-flex justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-4 d-flex justify-content-center">
                <div class="card main-card shadow-lg">
                    <div class="card-body text-center p-5">
                        <!-- Couple Names -->
                        <h1 class="couple-names mb-4">
                            <span class="name-1"><?php echo COUPLE_NAME_1; ?></span>
                            <span class="heart-icon mx-3">
                                <i class="fas fa-heart"></i>
                            </span>
                            <span class="name-2"><?php echo COUPLE_NAME_2; ?></span>
                        </h1>
                        
                        <!-- Wedding Date -->
                        <p class="wedding-date mb-4">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <?php echo WEDDING_DATE; ?>
                        </p>
                        
                        <!-- Welcome Message -->
                        <p class="welcome-message mb-4">
                            <?php echo WELCOME_MESSAGE; ?>
                        </p>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons d-flex justify-content-center flex-column align-items-center">
                            <button class="btn btn-primary btn-lg me-3 mb-4" style="width: max-content" id="viewGiftsBtn">
                                <i class="fas fa-gift me-2"></i>
                                Ver Lista de Presentes
                            </button>
                            
                            <button class="btn btn-outline-danger btn-sm " style="width: max-content" id="musicToggleBtn">
                                <i class="fas fa-pause me-2"></i>
                                <span id="musicToggleText">Pausar Áudio</span>
                            </button>
                        </div>
                        
                        <!-- Loading Spinner (hidden by default) -->
                        <div class="loading-spinner d-none mt-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
