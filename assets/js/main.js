/**
 * JavaScript principal do projeto
 * Funcionalidades da página inicial
 */

$(document).ready(function() {
    'use strict';
    
    // Variáveis globais
    let isMusicPlaying = false;
    let musicElement = document.getElementById('backgroundMusic');
    
    // Inicialização
    init();
    
    /**
     * Inicializa as funcionalidades da página
     */
    function init() {
        setupEventListeners();
        setupAudio();
        showWelcomeMessage();
        animateElements();
    }
    
    /**
     * Configura os event listeners
     */
    function setupEventListeners() {
        // Botão de música
        $('#musicToggleBtn').on('click', toggleMusic);
        
        // Botão de ver presentes
        $('#viewGiftsBtn').on('click', viewGifts);
        
        // Teclas de atalho
        $(document).on('keydown', handleKeyboard);
        
        // Resize da janela
        $(window).on('resize', handleResize);
    }
    
    /**
     * Configura o áudio de fundo
     */
    function setupAudio() {
        if (musicElement) {
            // Tentar reproduzir automaticamente (pode ser bloqueado pelo navegador)
            musicElement.volume = 0.3; // Volume baixo
            
            musicElement.addEventListener('canplaythrough', function() {
                console.log('Áudio carregado e pronto para reprodução');
            });
            
            musicElement.addEventListener('error', function(e) {
                console.warn('Erro ao carregar áudio:', e);
                showNotification('Áudio não disponível', 'warning');
            });
            
            // Tentar reproduzir após interação do usuário
            $(document).one('click keydown', function() {
                if (!isMusicPlaying) {
                    playMusic();
                }
            });
        }
    }
    
    /**
     * Alterna a reprodução da música
     */
    function toggleMusic() {
        if (isMusicPlaying) {
            pauseMusic();
        } else {
            playMusic();
        }
    }
    
    /**
     * Reproduz a música
     */
    function playMusic() {
        if (musicElement) {
            musicElement.play().then(() => {
                isMusicPlaying = true;
                updateMusicButton();
                showNotification('Música iniciada', 'success');
            }).catch(error => {
                console.warn('Erro ao reproduzir música:', error);
                showNotification('Não foi possível reproduzir a música', 'warning');
            });
        }
    }
    
    /**
     * Pausa a música
     */
    function pauseMusic() {
        if (musicElement) {
            musicElement.pause();
            isMusicPlaying = false;
            updateMusicButton();
            showNotification('Música pausada', 'info');
        }
    }
    
    /**
     * Atualiza o texto do botão de música
     */
    function updateMusicButton() {
        const button = $('#musicToggleBtn');
        const text = $('#musicToggleText');
        
        if (isMusicPlaying) {
            text.text('Pausar Música');
            button.removeClass('btn-outline-secondary').addClass('btn-outline-danger');
        } else {
            text.text('Reproduzir Música');
            button.removeClass('btn-outline-danger').addClass('btn-outline-secondary');
        }
    }
    
    /**
     * Exibe a lista de presentes (redireciona para área admin)
     */
    function viewGifts() {
        showLoading(true);
        
        // Simular carregamento
        setTimeout(() => {
            showLoading(false);
            showNotification('Redirecionando para área administrativa...', 'info');
            
            // Redirecionar para área administrativa
            setTimeout(() => {
                window.location.href = 'admin/login.php';
            }, 1000);
        }, 500);
    }
    
    /**
     * Mostra/esconde o spinner de carregamento
     */
    function showLoading(show) {
        const spinner = $('.loading-spinner');
        if (show) {
            spinner.removeClass('d-none');
        } else {
            spinner.addClass('d-none');
        }
    }
    
    /**
     * Exibe mensagem de boas-vindas
     */
    function showWelcomeMessage() {
        setTimeout(() => {
            showNotification('Bem-vindos ao nosso casamento! 💕', 'success');
        }, 1000);
    }
    
    /**
     * Anima elementos da página
     */
    function animateElements() {
        // Animar nomes do casal
        $('.name-1').addClass('slide-in-left');
        $('.name-2').addClass('slide-in-right');
        
        // Animar outros elementos com delay
        setTimeout(() => {
            $('.wedding-date').addClass('fade-in');
        }, 300);
        
        setTimeout(() => {
            $('.welcome-message').addClass('fade-in');
        }, 600);
        
        setTimeout(() => {
            $('.action-buttons').addClass('fade-in');
        }, 900);
    }
    
    /**
     * Manipula eventos de teclado
     */
    function handleKeyboard(e) {
        switch(e.key) {
            case ' ':
                e.preventDefault();
                toggleMusic();
                break;
            case 'Enter':
                if (e.target.id === 'viewGiftsBtn') {
                    viewGifts();
                }
                break;
            case 'Escape':
                pauseMusic();
                break;
        }
    }
    
    /**
     * Manipula redimensionamento da janela
     */
    function handleResize() {
        // Ajustar layout se necessário
        const windowWidth = $(window).width();
        
        if (windowWidth < 768) {
            // Layout mobile
            $('.main-card').css('margin', '20px');
        } else {
            // Layout desktop
            $('.main-card').css('margin', '0');
        }
    }
    
    /**
     * Exibe notificações toast
     */
    function showNotification(message, type = 'info') {
        const toast = $('#notificationToast');
        const toastMessage = $('#toastMessage');
        
        // Definir ícone baseado no tipo
        let icon = 'fas fa-info-circle';
        let iconColor = 'text-primary';
        
        switch(type) {
            case 'success':
                icon = 'fas fa-check-circle';
                iconColor = 'text-success';
                break;
            case 'warning':
                icon = 'fas fa-exclamation-triangle';
                iconColor = 'text-warning';
                break;
            case 'error':
                icon = 'fas fa-times-circle';
                iconColor = 'text-danger';
                break;
        }
        
        // Atualizar conteúdo
        toast.find('.toast-header i').removeClass().addClass(icon + ' ' + iconColor + ' me-2');
        toastMessage.text(message);
        
        // Mostrar toast
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
    }
    
    /**
     * Utilitários
     */
    
    // Função para formatar data
    function formatDate(date) {
        return new Intl.DateTimeFormat('pt-BR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }).format(date);
    }
    
    // Função para debounce
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Função para throttle
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    // Expor funções globais se necessário
    window.CasamentoApp = {
        toggleMusic: toggleMusic,
        viewGifts: viewGifts,
        showNotification: showNotification
    };
});
