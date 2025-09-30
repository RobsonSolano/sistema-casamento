<?php
require_once 'php/config.php';
require_once 'helpers/functions.php';
require_once 'functions/gifts_db.php';

// Verificar se gift_id foi fornecido
$giftId = $_GET['gift_id'] ?? null;
$returnUrl = $_GET['return_url'] ?? 'index.php';

if (!$giftId) {
    header('Location: index.php');
    exit;
}

// Buscar dados do presente
$gift = getGiftById($giftId);
if (!$gift) {
    header('Location: index.php');
    exit;
}

// Formatar presente para exibição
$formattedGift = formatGiftForDisplay($gift);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentear com PIX - <?php echo SITE_TITLE; ?></title>

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

<body>
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
            <div class="col-12 col-md-10 col-lg-8 mx-auto">
                <!-- Header -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">
                                <i class="fas fa-gift me-2"></i>
                                Presentear com PIX
                            </h4>
                            <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Voltar
                            </a>
                        </div>
                    </div>
                    <div class="card-body text-center py-4">
                        <h1 class="couple-names mb-3">
                            <span class="name-1"><?php echo COUPLE_NAME_1; ?></span>
                            <span class="heart-icon mx-3">
                                <i class="fas fa-heart"></i>
                            </span>
                            <span class="name-2"><?php echo COUPLE_NAME_2; ?></span>
                        </h1>
                        <p class="wedding-date mb-0" style="color: #4169e1; text-shadow: none; font-weight: 700;">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <?php echo WEDDING_DATE; ?>
                        </p>
                    </div>
                </div>

                <!-- Informações do Presente -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-body text-center">
                        <h5 class="card-title text-muted mb-3" style="border-bottom: 1px solid #f1f1f1; padding-bottom: 1em; font-weight: 700;">Presente Selecionado</h5>
                        <div class="d-flex align-items-center flex-column flex-md-row justify-content-between">
                            <h3 class="text-primary mb-1 fw-bolder" id="checkoutGiftName"><?php echo htmlspecialchars($formattedGift['titulo']); ?></h3>
                            <h2 class="text-success fw-bold mb-0" id="checkoutGiftValue"><?php echo $formattedGift['valor']; ?></h2>
                        </div>
                    </div>
                </div>

                <!-- Passo 1: Seus Dados -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Passo 1: Seus Dados
                        </h5>
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

                <!-- Passo 2: Chave PIX -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Passo 2: Chave PIX
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Copie a chave PIX abaixo e faça o PIX com o valor de <strong id="checkoutGiftValueStep2"><?php echo $formattedGift['valor']; ?></strong>
                        </p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg text-center fw-bold"
                                id="pixKeyDisplay" value="<?php echo PIX_KEY; ?>" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyPixKey()">
                                <i class="fas fa-copy me-1"></i>
                                Copiar
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Esta é a chave PIX oficial da <?php echo PIX_OWNER_NAME; ?>
                        </small>
                    </div>
                </div>

                <!-- Passo 3: WhatsApp -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fab fa-whatsapp me-2"></i>
                            Passo 3: Enviar Comprovante
                        </h5>
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

                <!-- Passo 4: Confirmar Envio -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Passo 4: Confirmar Envio
                        </h5>
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

    <!-- Configuração JavaScript -->
    <script>
        // Configuração global do projeto
        window.BASE_URL = '<?php echo BASE_URL; ?>';

        // Dados do presente para JavaScript
        window.checkoutData = {
            giftId: <?php echo $giftId; ?>,
            giftName: '<?php echo addslashes($formattedGift['titulo']); ?>',
            giftValue: '<?php echo $formattedGift['valor']; ?>'
        };
    </script>

    <!-- PIX Functions JS -->
    <script src="assets/js/pix-functions.js"></script>

    <!-- Music Controller JS -->
    <script src="assets/js/music-controller.js"></script>

    <script>
        $(document).ready(function() {
            // Gerar link do WhatsApp ao carregar a página
            generateWhatsAppLink();

            // Atualizar link quando nome for digitado
            $('#donorName').on('input', function() {
                generateWhatsAppLink();
            });
        });

        /**
         * Gera link do WhatsApp com mensagem pré-formatada
         */
        function generateWhatsAppLink() {
            const giftName = window.checkoutData.giftName;
            const giftValue = window.checkoutData.giftValue;
            const donorName = $('#donorName').val().trim() || '[SEU NOME]';

            const message = `Olá Marislan! 👋

Acabei de fazer um PIX para o presente "${giftName}" no valor de ${giftValue}.

Aqui está o comprovante do pagamento:

[ANEXAR COMPROVANTE AQUI]

Muito obrigado(a) pelo convite! 
Que vocês sejam muito felizes! 💕

Atenciosamente,
${donorName}`;

            const whatsappNumber = '5511996271186'; // Número da Marislan
            const encodedMessage = encodeURIComponent(message);
            const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

            $('#whatsappLink').attr('href', whatsappLink);
        }

        /**
         * Mostra notificação toast
         */
        function showNotification(message, type = 'info') {
            // Remover notificações existentes
            $('.toast-notification').remove();

            // Criar nova notificação
            const toast = $(`
                <div class="toast-notification toast-${type}">
                    <div class="toast-content">
                        <i class="fas fa-${getIconForType(type)} me-2"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `);

            // Adicionar ao body
            $('body').append(toast);

            // Mostrar com animação
            setTimeout(() => {
                toast.addClass('show');
            }, 100);

            // Remover após 4 segundos
            setTimeout(() => {
                toast.removeClass('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        }

        /**
         * Retorna ícone baseado no tipo de notificação
         */
        function getIconForType(type) {
            const icons = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle',
                'danger': 'times-circle'
            };
            return icons[type] || 'info-circle';
        }
    </script>
</body>

</html>