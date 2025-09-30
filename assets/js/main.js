/**
 * JavaScript principal do projeto
 * Funcionalidades da página inicial
 */

$(document).ready(function() {
    'use strict';
    
    // Variáveis globais
    let welcomeModal;
    
    // Configuração global para modais mais rápidos
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Reduzir tempo de transição padrão do Bootstrap
        bootstrap.Modal.prototype._config = bootstrap.Modal.prototype._config || {};
        bootstrap.Modal.prototype._config.backdropTransitionDuration = 150;
        bootstrap.Modal.prototype._config.scrollTransitionDuration = 150;
    }
    
    // Inicialização
    init();
    
    /**
     * Inicializa as funcionalidades da página
     */
    function init() {
        setupEventListeners();
        setupWelcomeModal();
        animateElements();
        startCountdown();
        
        // Verificar sessão antes de mostrar modal - reduzido delay
        setTimeout(() => {
            if (shouldShowWelcomeModal()) {
                showWelcomeModal();
            }
            // A música será gerenciada pelo MusicController
        }, 300);
    }
    
    /**
     * Inicia o contador regressivo
     */
    function startCountdown() {
        // Data do casamento: 13 de dezembro de 2025
        const weddingDate = new Date('2025-12-13T00:00:00');
        
        function updateCountdown() {
            const now = new Date();
            const timeLeft = weddingDate - now;
            
            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                // Atualizar os elementos HTML
                $('#days').text(days.toString().padStart(2, '0'));
                $('#hours').text(hours.toString().padStart(2, '0'));
                $('#minutes').text(minutes.toString().padStart(2, '0'));
                $('#seconds').text(seconds.toString().padStart(2, '0'));
            } else {
                // Casamento já aconteceu
                $('#days').text('00');
                $('#hours').text('00');
                $('#minutes').text('00');
                $('#seconds').text('00');
            }
        }
        
        // Atualizar imediatamente
        updateCountdown();
        
        // Atualizar a cada segundo
        setInterval(updateCountdown, 1000);
    }
    
    /**
     * Configura o modal de boas-vindas
     */
    function setupWelcomeModal() {
        welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
        
        // Event listener para o botão OK
        $('#welcomeOkBtn').on('click', function() {
            activateAudioAndCloseModal();
        });
        
        // Event listener para clicar fora do modal
        $('#welcomeModal').on('click', function(e) {
            if (e.target === this) {
                activateAudioAndCloseModal();
            }
        });
    }
    
    /**
     * Mostra o modal de boas-vindas
     */
    function showWelcomeModal() {
        if (welcomeModal) {
            welcomeModal.show();
        }
    }
    
    /**
     * Fecha o modal de boas-vindas e inicia a música
     */
    function activateAudioAndCloseModal() {
        if (welcomeModal) {
            welcomeModal.hide();
            showNotification('🎵 Bem-vindos ao nosso casamento! 💕', 'success');
            
            // Criar sessão para não mostrar o modal novamente
            createWelcomeSession();
            
            // Iniciar música após fechar o modal - reduzido delay
            setTimeout(() => {
                tryStartMusic();
            }, 200);
        }
    }
    
    /**
     * Configura os event listeners
     */
    function setupEventListeners() {
        // Formulário de recados
        $('#guestbookForm').on('submit', function(e) {
            e.preventDefault();
            handleGuestbookSubmit();
        });
        
        // Botão "Ver Todos os Presentes"
        $('#viewAllGiftsBtn').click(function() {
            window.location.href = window.BASE_URL + '/presentes';
        });
        
        // Animações ao scroll
        $(window).on('scroll', function() {
            animateOnScroll();
        });
    }
    
    /**
     * Processa o envio do formulário de recados
     */
    function handleGuestbookSubmit() {
        const form = $('#guestbookForm');
        const submitBtn = $('#submitRecadoBtn');
        const btnText = $('.btn-text');
        const btnLoading = $('.btn-loading');
        
        const formData = {
            nome: $('#guestName').val().trim(),
            mensagem: $('#guestMessage').val().trim()
        };
        
        // Validação
        if (!formData.nome || !formData.mensagem) {
            showNotification('Por favor, preencha todos os campos obrigatórios.', 'warning');
            return;
        }
        
        if (formData.mensagem.length < 10) {
            showNotification('A mensagem deve ter pelo menos 10 caracteres.', 'warning');
            return;
        }
        
        // Desabilitar botão e mostrar loading
        submitBtn.prop('disabled', true);
        btnText.addClass('d-none');
        btnLoading.removeClass('d-none');
        
        // Enviar via AJAX
        $.ajax({
            url: window.BASE_URL + '/api/send_recado',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    form[0].reset();
            } else {
                    showNotification(response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Ops! Algo deu errado. Tente novamente.', 'danger');
            },
            complete: function() {
                // Reabilitar botão
                submitBtn.prop('disabled', false);
                btnText.removeClass('d-none');
                btnLoading.addClass('d-none');
            }
        });
    }
    
    /**
     * Anima elementos da página
     */
    function animateElements() {
        // Animar elementos com classe 'animate-on-scroll'
        $('.animate-on-scroll').each(function() {
            const element = $(this);
            const animation = element.data('animation') || 'fadeInUp';
            const delay = element.data('delay') || 0;
            
            element.css('opacity', '0');
            element.addClass('animate__animated animate__' + animation);
        
        setTimeout(() => {
                element.css('opacity', '1');
            }, delay);
        });
    }
    
    /**
     * Anima elementos durante o scroll
     */
    function animateOnScroll() {
        $('.animate-on-scroll').each(function() {
            const element = $(this);
            const elementTop = element.offset().top;
            const elementBottom = elementTop + element.outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                element.addClass('animate__animated animate__fadeInUp');
            }
        });
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
     * Verifica se deve mostrar o modal de boas-vindas
     */
    function shouldShowWelcomeModal() {
        const sessionData = localStorage.getItem('casamento_welcome_session');
        
        if (!sessionData) {
            return true; // Não há sessão, mostrar modal
        }
        
        try {
            const session = JSON.parse(sessionData);
            const now = Date.now();
            const oneHourInMs = 60 * 60 * 1000; // 1 hora em milissegundos
            
            // Verificar se a sessão ainda é válida (menos de 1 hora)
            if (now - session.timestamp < oneHourInMs) {
                return false; // Sessão válida, não mostrar modal
            } else {
                // Sessão expirada, remover e mostrar modal
                localStorage.removeItem('casamento_welcome_session');
                return true;
            }
        } catch (error) {
            // Erro ao parsear, remover dados corrompidos e mostrar modal
            localStorage.removeItem('casamento_welcome_session');
            return true;
        }
    }
    
    /**
     * Cria uma sessão de boas-vindas
     */
    function createWelcomeSession() {
        const sessionData = {
            timestamp: Date.now(),
            version: '1.0'
        };
        
        localStorage.setItem('casamento_welcome_session', JSON.stringify(sessionData));
    }
    
    /**
     * Tenta iniciar a música de várias formas
     */
    function tryStartMusic() {
        if (window.MusicController && window.MusicController.playMusic) {
            window.MusicController.playMusic();
        }
    }
    
    /**
     * Destrói a sessão de boas-vindas (para debug ou reset)
     */
    function destroyWelcomeSession() {
        localStorage.removeItem('casamento_welcome_session');
        showNotification('Sessão resetada. Modal será exibido novamente.', 'info');
    }
    
    /**
     * Função de debug para verificar status da sessão e música
     */
    function debugSessionAndMusic() {
        const sessionData = localStorage.getItem('casamento_welcome_session');
        const musicElement = document.getElementById('backgroundMusic');
        
        console.log('=== DEBUG SESSÃO E MÚSICA ===');
        console.log('Sessão ativa:', sessionData ? 'Sim' : 'Não');
        
        if (sessionData) {
            try {
                const session = JSON.parse(sessionData);
                const now = Date.now();
                const timeDiff = now - session.timestamp;
                const oneHourInMs = 60 * 60 * 1000;
                
                console.log('Timestamp da sessão:', new Date(session.timestamp));
                console.log('Tempo decorrido:', Math.round(timeDiff / 1000 / 60), 'minutos');
                console.log('Sessão válida:', timeDiff < oneHourInMs ? 'Sim' : 'Não');
            } catch (error) {
                console.log('Erro ao parsear sessão:', error);
            }
        }
        
        console.log('Elemento de música encontrado:', musicElement ? 'Sim' : 'Não');
        if (musicElement) {
            console.log('Música pausada:', musicElement.paused);
            console.log('Tempo atual:', musicElement.currentTime);
            console.log('Volume:', musicElement.volume);
        }
        
        console.log('MusicController disponível:', window.MusicController ? 'Sim' : 'Não');
        if (window.MusicController) {
            console.log('Música tocando (MusicController):', window.MusicController.isMusicPlaying);
        }
        
        return {
            hasSession: !!sessionData,
            musicElement: musicElement,
            musicPaused: musicElement ? musicElement.paused : null,
            musicController: window.MusicController
        };
    }

    // Expor funções globalmente se necessário
    window.MainController = {
        showNotification: showNotification,
        showWelcomeModal: showWelcomeModal,
        destroyWelcomeSession: destroyWelcomeSession,
        shouldShowWelcomeModal: shouldShowWelcomeModal,
        debugSessionAndMusic: debugSessionAndMusic,
        tryStartMusic: tryStartMusic
    };
});