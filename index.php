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
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content w-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-10 col-xl-10">
                        <div class="couple-initials mb-4 d-flex flex-column align-items-center justify-content-center gap-3">
                            <ha class="h4 couple-signature principal">Marislan <br class="d-block d-md-none">& <br class="d-block d-md-none">Douglas</ha>

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
                    <p class="gallery-text">Clique para ver nossas fotos</p>
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
                        <button class="btn btn-info" id="exitFullscreen">
                            SAIR
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body fullscreen-modal-body">
                    <div class="fullscreen-image-container">
                        <div class="gallery-loading" id="galleryLoading">
                            <div class="spinner-border text-light" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
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
                            <h2 class="church-title">💒 Nossa Igreja</h2>
                            <p class="church-description">
                                Escolhemos celebrar o nosso casamento na Paróquia Nossa Senhora Aparecida, um lugar que carrega um significado muito especial para nós. 💙
                                Temos uma grande devoção a Nossa Senhora Aparecida, e é sob o seu olhar amoroso que queremos abençoar nossa união e iniciar este novo capítulo de nossas vidas.
                            </p>
                            <p class="church-description">
                                Atualmente, a Paróquia Nossa Senhora Aparecida está rodeada por aproximadamente 30 mil pessoas e reúne 9 comunidades: Santa Rita de Cássia, Nossa Senhora de Fátima, Nossa Senhora do Cenáculo, São Cristóvão, Santa Ifigênia, Santa Cruz e as duas mais novas, Santa Marta e Santa Terezinha, além de São João Paulo II.
                                Com alegria, está nascendo ainda a 10ª comunidade, São José, um símbolo de fé e renovação.
                            </p>
                            <p class="church-description">
                                Hoje, a paróquia é conduzida pelo Pe. Luiz Marin, que com dedicação e carinho guia os fiéis e mantém viva a missão de Nossa Senhora Aparecida: acolher, inspirar e fortalecer os corações na fé.

                                É neste lugar abençoado que diremos “sim” diante de Deus e de todos que amamos. 💍✨
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
                            <h4 class="couple-signature" style="font-size: 2.5rem; font-weight: 500 !important;">Marislan <br class="d-block d-md-none">& <br class="d-block d-md-none">Douglas</h4>
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
            // Buscar imagens da API usando URL relativa
            const apiUrl = 'api/list_gallery_images.php';

            console.log('Tentando carregar galeria de:', apiUrl);

            fetch(apiUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                    cache: 'no-cache'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);

                    if (data.success && data.images && data.images.length > 0) {
                        // Mapear URLs das imagens
                        galleryImages = data.images.map(img => img.url);
                        console.log('Galeria carregada com', galleryImages.length, 'imagens');
                    } else {
                        console.warn('Nenhuma imagem encontrada, usando fallback');
                        // Fallback: usar imagem padrão se não houver fotos
                        galleryImages = ['assets/images/galeria/casal.webp'];
                    }
                    renderGallery();
                })
                .catch(error => {
                    console.error('Erro ao carregar imagens da galeria:', error);
                    console.error('Erro detalhado:', error.message);

                    // Fallback em caso de erro: carregar imagens manualmente
                    console.log('Usando fallback: lista manual de imagens');
                    galleryImages = [
                        'assets/images/galeria/Foto (1).png',
                        'assets/images/galeria/Foto (2).png',
                        'assets/images/galeria/Foto (3).png',
                        'assets/images/galeria/Foto (4).png',
                        'assets/images/galeria/Foto (5).png',
                        'assets/images/galeria/Foto (6).png',
                        'assets/images/galeria/Foto (7).png',
                        'assets/images/galeria/Foto (8).png',
                        'assets/images/galeria/Foto (9).png',
                        'assets/images/galeria/Foto (10).png',
                        'assets/images/galeria/Foto (11).png',
                        'assets/images/galeria/Foto (12).png',
                        'assets/images/galeria/Foto (13).png',
                        'assets/images/galeria/Foto (14).png',
                        'assets/images/galeria/Foto (15).png',
                        'assets/images/galeria/Foto (16).png',
                        'assets/images/galeria/Foto (17).png',
                        'assets/images/galeria/Foto (18).png',
                        'assets/images/galeria/Foto (19).png',
                        'assets/images/galeria/Foto (20).png',
                        'assets/images/galeria/Foto (21).png',
                        'assets/images/galeria/Foto (22).png',
                        'assets/images/galeria/Foto (23).png',
                        'assets/images/galeria/Foto (24).png',
                        'assets/images/galeria/Foto (25).png',
                        'assets/images/galeria/Foto (26).png'
                    ];
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

        // Carregar imagem com loading
        function loadImageWithLoading(imageSrc, callback) {
            const fullscreenImage = document.getElementById('fullscreenImage');
            const loadingEl = document.getElementById('galleryLoading');

            // Mostrar loading
            loadingEl.classList.add('active');
            fullscreenImage.classList.add('loading');

            // Criar nova imagem para pré-carregar
            const img = new Image();

            img.onload = function() {
                // Esconder loading
                loadingEl.classList.remove('active');
                fullscreenImage.classList.remove('loading');

                // Atualizar src da imagem
                fullscreenImage.src = imageSrc;

                if (callback) callback();
            };

            img.onerror = function() {
                // Esconder loading mesmo em erro
                loadingEl.classList.remove('active');
                fullscreenImage.classList.remove('loading');

                // Tentar carregar mesmo assim
                fullscreenImage.src = imageSrc;

                if (callback) callback();
            };

            // Iniciar carregamento
            img.src = imageSrc;
        }

        // Abrir tela cheia
        function openFullscreen() {
            const fullscreenImage = document.getElementById('fullscreenImage');
            const fullscreenTitle = document.getElementById('fullscreenImageTitle');

            fullscreenTitle.textContent = `${currentImageIndex + 1} de ${galleryImages.length}`;

            const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
            fullscreenModal.show();

            // Carregar primeira imagem com loading
            loadImageWithLoading(galleryImages[currentImageIndex]);
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
            const fullscreenTitle = document.getElementById('fullscreenImageTitle');

            fullscreenTitle.textContent = `${currentImageIndex + 1} de ${galleryImages.length}`;

            // Carregar próxima imagem com loading
            loadImageWithLoading(galleryImages[currentImageIndex]);
        }

        function fullscreenPrev() {
            prevSlide();
            const fullscreenTitle = document.getElementById('fullscreenImageTitle');

            fullscreenTitle.textContent = `${currentImageIndex + 1} de ${galleryImages.length}`;

            // Carregar imagem anterior com loading
            loadImageWithLoading(galleryImages[currentImageIndex]);
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