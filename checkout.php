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

<body id="checkout">
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
                                Pix de presente
                            </h4>

                        </div>
                    </div>
                    <div class="card-body text-center">
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
                    <div class="card-body d-flex flex-column flex-md-row gap-3 justify-content-between">
                        <div class="mb-3 w-100">
                            <label for="donorName" class="form-label">Seu Nome *</label>
                            <input type="text" class="form-control" id="donorName"
                                placeholder="Informe seu nome" required>
                            <small class="text-muted">Este nome aparecerá no histórico de presentes</small>
                        </div>
                    </div>
                </div>

                <!-- Passo 2: Pagamento PIX -->
                <div class="card main-card shadow-lg mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Passo 2: Pagamento PIX
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Ao fazer o PIX, informe o valor de <strong id="checkoutGiftValueStep2"><?php echo $formattedGift['valor']; ?></strong> para o presente "<strong><?php echo htmlspecialchars($formattedGift['titulo']); ?></strong>"
                        </div>

                        <div class="row g-4">
                            <!-- Chave PIX - Lado Esquerdo -->
                            <div class="col-12 col-md-6">
                                <div class="pix-method-card">
                                    <div class="method-header mb-3">
                                        <i class="fas fa-key text-primary me-2"></i>
                                        <h6 class="mb-0 fw-bold">Chave PIX</h6>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        Copie a chave PIX abaixo e faça o PIX no seu app bancário
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
                                        <i class="fas fa-info-circle me-1"></i> Dados do PIX:<br><br>
                                        <strong class="text-dark fw-bold">Nome:</strong> Marislan Ribeiro Ferreira<br>
                                        <strong class="text-dark fw-bold">CPF:</strong> •••.346.368-••<br>
                                        <strong class="text-dark fw-bold">Banco:</strong> 260 - Nu Pagamentos S.A. - Instituição de Pagamento
                                    </small>
                                </div>
                            </div>

                            <!-- QR Code - Lado Direito -->
                            <div class="col-12 col-md-6">
                                <div class="pix-method-card">
                                    <div class="method-header mb-3">
                                        <i class="fas fa-qrcode text-primary me-2"></i>
                                        <h6 class="mb-0 fw-bold">QR Code PIX</h6>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        Escaneie o QR Code com seu app bancário
                                    </p>
                                    <div id="qrCodeContainer" style="display: none;">
                                        <div class="qr-code-display mb-3">
                                            <img src="assets/images/qr-code-pix.png" alt="QR Code PIX" class="img-fluid" style="max-width: 200px;">
                                        </div>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control form-control-sm text-center fw-bold"
                                                id="pixCodeDisplay"
                                                value="00020126360014BR.GOV.BCB.PIX0114+55119536903035204000053039865802BR5925Marislan Ribeiro Ferreira6009SAO PAULO62140510EemqjsCrhS63044CB5"
                                                readonly>
                                            <button class="btn btn-outline-info btn-sm" type="button" onclick="copyPixCode()">
                                                <i class="fas fa-copy me-1"></i>
                                                Copiar
                                            </button>
                                        </div>
                                    </div>
                                    <div id="qrCodeButton" class="d-flex justify-content-center">
                                        <button class="btn btn-primary btn-lg" type="button" onclick="showQRCode()">
                                            <i class="fas fa-qrcode me-2"></i>
                                            Exibir QR Code
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Ou copie o código PIX acima
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between">

                    <!-- Passo 4: Confirmar Envio -->
                    <div class="card main-card shadow-lg mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Passo 3: Confirmar Envio
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <p class="text-muted mb-3">
                                Após ter feito o pagamento clique em finalizar
                            </p>
                            <button id="confirmGiftBtn" class="btn btn-warning btn-lg" onclick="confirmGift()">
                                <i class="fas fa-gift me-2"></i>
                                Confirmar e finalizar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal de Agradecimento -->
                <div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <div class="mb-4">
                                    <i class="fas fa-heart fa-4x text-success"></i>
                                </div>
                                <h3 class="modal-title mb-3" id="thankYouModalLabel">
                                    Muito Obrigado! 💕
                                </h3>
                                <p class="mb-4 text-muted">
                                    Sua contribuição é muito especial para nós! <br>
                                    <strong>Marislan e Douglas</strong>
                                </p>
                                <p class="mb-4">
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Presente confirmado com sucesso!
                                    </small>
                                </p>
                                <button type="button" class="btn btn-success btn-lg px-5" id="thankYouOkBtn">
                                    <i class="fas fa-home me-2"></i>
                                    Voltar ao Início
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <a href="<?php echo htmlspecialchars($returnUrl); ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Voltar
                    </a>
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

    <!-- Checkout Custom JS (primeiro para definir funções) -->
    <script src="assets/js/checkout-custom.js"></script>

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

            // Configurar modal de agradecimento
            const thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));

            // Event listener para o botão do modal de agradecimento
            $('#thankYouOkBtn').on('click', function() {
                const returnUrl = new URLSearchParams(window.location.search).get('return_url') || 'index.php';
                window.location.href = returnUrl;
            });

            // Expor modal globalmente para uso nas funções PIX
            window.thankYouModal = thankYouModal;
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

        /**
         * Copia o código PIX para a área de transferência
         */
        function copyPixCode() {
            const pixCodeInput = document.getElementById('pixCodeDisplay');

            // Selecionar o texto
            pixCodeInput.select();
            pixCodeInput.setSelectionRange(0, 99999); // Para mobile

            try {
                // Copiar para área de transferência
                document.execCommand('copy');

                // Feedback visual
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado!';
                btn.classList.remove('btn-outline-info');
                btn.classList.add('btn-success');

                // Mostrar notificação
                showNotification('Código PIX copiado com sucesso!', 'success');

                // Restaurar botão após 2 segundos
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-info');
                }, 2000);

            } catch (err) {
                console.error('Erro ao copiar:', err);
                showNotification('Erro ao copiar código PIX', 'error');
            }
        }
    </script>
</body>

</html>