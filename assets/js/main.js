/**
 * JavaScript principal do projeto
 * Funcionalidades da página inicial
 */

$(document).ready(function() {
    'use strict';
    
    // Variáveis globais
    let welcomeModal;
    
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
        
        // Mostrar modal de boas-vindas após um pequeno delay
        setTimeout(() => {
            showWelcomeModal();
        }, 1000);
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
            
            // Iniciar música após fechar o modal
            setTimeout(() => {
                if (window.MusicController && window.MusicController.playMusic) {
                    window.MusicController.playMusic();
                }
            }, 500);
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
    
    // Expor funções globalmente se necessário
    window.MainController = {
        showNotification: showNotification,
        showWelcomeModal: showWelcomeModal
    };
});