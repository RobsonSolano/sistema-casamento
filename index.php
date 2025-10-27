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
                    <div class="col-12 col-md-10 col-lg-10 col-xl-10">
                        <div class="couple-initials mb-4 d-flex flex-column align-items-center justify-content-center gap-3">
                            <ha class="h4 couple-signature principal">Marislan & Douglas</ha>

                            <!-- Wedding Date -->
                            <h2 class="h2 wedding-date mb-4  text-white d-flex align-items-center" style="font-family: 'Tempting', cursive !important;">
                                <span>13</span><span style="margin-top:-20px">.</span><span>12</span><span style="margin-top:-20px">.</span><span>25</span>
                            </h2>

                            <div class="d-block mb-3" style=" font-weight: 500; text-shadow: 0px 0px 10px #000;">
                                <p class="text-center text-white" style="line-height: 1.3;;     font-size: 1.2rem;">Se você está aqui, é porque faz parte da nossa história!</p>
                                <p class="text-white" style="line-height: 1.3;; text-align: justify;    font-size: 1.2rem;">

                                    Amigos e familiares, é com imensa alegria que convidamos vocês para o nosso casamento!

                                    Depois de 14 anos juntos, entre risadas, aventuras, desafios e muito amor, percebemos que faltava algo essencial: o sacramento do matrimônio.
                                    <br>
                                    <br>
                                    Mesmo fazendo tudo ao contrário (como sempre foi o nosso estilo), entendemos que chegou o momento de entregar oficialmente nossa união nas mãos de Deus, que sempre foi o centro da nossa caminhada.

                                    Queremos viver este dia rodeados de pessoas especiais, porque não teria graça sem a sua presença para tornar tudo ainda mais alegre, abençoado e inesquecível.
                                </p>
                                <p class="d-block  text-white text-center" style="font-style: italic; font-weight: 500;     font-size: 1.2rem; text-shadow: 0px 0px 10px #000;">
                                    Sua presença tornará este dia ainda mais inesquecível.
                                </p>
                            </div>

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
                                <div class="col-12 col-sm-6 col-md-6 col-xl-4">
                                    <div class="gift-preview-card">
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
                                        <h5 class="card-title text-center"><?php echo htmlspecialchars($gift['titulo']); ?></h5>

                                        <!-- Preço -->
                                        <div class="price-section mb-3">
                                            <h5 class="text-primary mb-0 text-center fw-bold"><?php echo $gift['valor']; ?></h5>
                                        </div>

                                        <!-- Botão de Ação -->
                                        <div class="mt-auto d-flex justify-content-center">
                                            <a href="checkout.php?gift_id=<?php echo $gift['id']; ?>&return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
                                                class="btn btn-primary w-75 checkout-btn d-flex align-items-center justify-content-center gap-2">
                                                <i class="fas fa-gift me-2"></i>
                                                <span class="d-none d-md-block">Comprar</span>
                                                <span class="d-block d-md-none">Comprar</span>
                                            </a>
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


    <!-- Seção Galeria de Fotos -->
    <section class="gallery-section">
        <div class="gallery-background">
            <div class="gallery-overlay"></div>
            <div class="gallery-content">
                <div class="play-button-container">
                    <button class="play-button" id="openGalleryBtn">
                        <i class="fas fa-play"></i>
                    </button>
                    <p class="gallery-text">Clique para ver nossas fotos em tela cheia</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Modal de Tela Cheia -->
    <div class="modal fade" id="fullscreenModal" tabindex="-1" aria-labelledby="fullscreenModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content fullscreen-modal-content">
                <div class="modal-header fullscreen-modal-header">
                    <h5 class="modal-title" id="fullscreenModalLabel">
                        <i class="fas fa-image me-2"></i>
                        <span id="fullscreenImageTitle">Foto 1 de 5</span>
                    </h5>
                    <div class="fullscreen-controls">
                        <button class="gallery-btn gallery-prev" id="fullscreenPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="gallery-btn gallery-next" id="fullscreenNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="gallery-btn gallery-fullscreen" id="exitFullscreen">
                            <i class="fas fa-compress"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body fullscreen-modal-body">
                    <div class="fullscreen-image-container">
                        <img id="fullscreenImage" src="" alt="Imagem em tela cheia" class="fullscreen-image">
                    </div>
                </div>
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

    <!-- Seção da Igreja -->
    <section class="church-section">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-12 col-lg-6">
                    <div class="church-content">
                        <div class="church-text">
                            <h2 class="church-title">Paróquia Nossa Senhora Aparecida</h2>
                            <p class="church-description">
                                A Paróquia Nossa Senhora Aparecida em Salto, SP, não é a igreja mais famosa da cidade, que é a Igreja de Nossa Senhora do Monte Serrat.
                            </p>
                            <p class="church-description">
                                A história da "Igrejinha do Salto", como é conhecida, está ligada à fundação da cidade por Antônio Vieira Tavares e a devoção mariana do local, que é um dos maiores pontos turísticos e religiosos de Salto.

                            </p>
                            <p class="church-description">
                                A igreja, que foi restaurada em 2021, tem um centro histórico, religioso e cultural que atrai visitantes de todo o Brasil.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="church-image">
                        <img src="<?php echo base_url('assets/images/foto-igreja.webp') ?>"
                            alt="Paróquia Nossa Senhora Aparecida"
                            class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="footer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="footer-content text-center">
                        <div class="footer-icon mb-4">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="footer-title mb-3">Agradecemos pela visita</h3>
                        <p class="footer-message mb-4">
                            E aguardamos essa data tão especial ao lado de vocês,
                            que fazem parte da nossa história de amor.
                        </p>
                        <div class="footer-signature">
                            <p class="mb-4" style="font-size: 1.8rem;">Com carinho</p>
                            <h4 class="couple-signature" style="font-size: 2.5rem; font-weight: 500 !important;">Marislan & Douglas</h4>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </footer>
    <div class="rodape-final container-fluid bg-dark text-white py-3 px-4">
        <div class="d-flex justify-content-center justify-content-lg-end py-4 gap-2">
            <span style="font-family: 'Inter', serif;"></span>Desenvolvido por:
            <a href="https://api.whatsapp.com/send/?phone=5511996271186" class="text-info" target="_blank">
                <i class="fab fa-whatsapp"></i>
                Robson Solano
            </a>
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

    <!-- Gallery JavaScript -->
    <script>
        // Galeria de fotos
        let currentImageIndex = 0;
        let galleryImages = [];

        // Carregar imagens da galeria
        function loadGalleryImages() {
            // Buscar imagens da API
            fetch('api/list_gallery_images.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.images.length > 0) {
                        // Mapear URLs das imagens
                        galleryImages = data.images.map(img => img.url);
                    } else {
                        // Fallback: usar imagem padrão se não houver fotos
                        galleryImages = ['assets/images/galeria/casal.png'];
                    }
                    renderGallery();
                })
                .catch(error => {
                    console.error('Erro ao carregar imagens da galeria:', error);
                    // Fallback em caso de erro
                    galleryImages = ['assets/images/galeria/casal.png'];
                    renderGallery();
                });
        }

        // Renderizar galeria (simplificado para tela cheia)
        function renderGallery() {
            // Apenas carregar as imagens, não renderizar elementos do modal
            // As imagens serão exibidas diretamente na tela cheia
        }

        // Ir para slide específico
        function goToSlide(index) {
            currentImageIndex = index;
        }

        // Próximo slide
        function nextSlide() {
            const nextIndex = (currentImageIndex + 1) % galleryImages.length;
            goToSlide(nextIndex);
        }

        // Slide anterior
        function prevSlide() {
            const prevIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            goToSlide(prevIndex);
        }

        // Abrir tela cheia
        function openFullscreen() {
            const fullscreenImage = document.getElementById('fullscreenImage');
            const fullscreenTitle = document.getElementById('fullscreenImageTitle');

            fullscreenImage.src = galleryImages[currentImageIndex];
            fullscreenTitle.textContent = `Foto ${currentImageIndex + 1} de ${galleryImages.length}`;

            const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
            fullscreenModal.show();
        }

        // Fechar tela cheia
        function closeFullscreen() {
            const fullscreenModal = bootstrap.Modal.getInstance(document.getElementById('fullscreenModal'));
            if (fullscreenModal) {
                fullscreenModal.hide();
            }
        }

        // Navegar na tela cheia
        function fullscreenNext() {
            nextSlide();
            const fullscreenImage = document.getElementById('fullscreenImage');
            const fullscreenTitle = document.getElementById('fullscreenImageTitle');

            fullscreenImage.src = galleryImages[currentImageIndex];
            fullscreenTitle.textContent = `Foto ${currentImageIndex + 1} de ${galleryImages.length}`;
        }

        function fullscreenPrev() {
            prevSlide();
            const fullscreenImage = document.getElementById('fullscreenImage');
            const fullscreenTitle = document.getElementById('fullscreenImageTitle');

            fullscreenImage.src = galleryImages[currentImageIndex];
            fullscreenTitle.textContent = `Foto ${currentImageIndex + 1} de ${galleryImages.length}`;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Carregar imagens da galeria
            loadGalleryImages();

            // Botão para abrir galeria diretamente em tela cheia
            document.getElementById('openGalleryBtn').addEventListener('click', function() {
                openFullscreen();
            });


            // Controles da tela cheia
            document.getElementById('fullscreenNext').addEventListener('click', fullscreenNext);
            document.getElementById('fullscreenPrev').addEventListener('click', fullscreenPrev);
            document.getElementById('exitFullscreen').addEventListener('click', closeFullscreen);

            // Navegação por teclado
            document.addEventListener('keydown', function(e) {
                const fullscreenModal = document.getElementById('fullscreenModal');

                if (fullscreenModal.classList.contains('show')) {
                    if (e.key === 'ArrowRight') fullscreenNext();
                    if (e.key === 'ArrowLeft') fullscreenPrev();
                    if (e.key === 'Escape') closeFullscreen();
                }
            });
        });
    </script>
</body>

</html>