/**
 * JavaScript principal do projeto
 * Funcionalidades da página inicial
 */

$(document).ready(function() {
    'use strict';
    
    // Variáveis globais
    let isMusicPlaying = false;
    let musicElement = document.getElementById('backgroundMusic');
    let welcomeModal;
    let audioPermissionGranted = false;
    let musicStartTime = 0;
    
    // Inicialização
    init();
    
    /**
     * Inicializa as funcionalidades da página
     */
    function init() {
        setupEventListeners();
        setupAudio();
        setupWelcomeModal();
        animateElements();
        
        // Verificar se a música estava tocando em outra página
        restoreMusicState();
        
        // Mostrar modal de boas-vindas após um pequeno delay
        setTimeout(() => {
            showWelcomeModal();
        }, 1000);
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
     * Ativa o áudio e fecha o modal
     */
    function activateAudioAndCloseModal() {
        if (!audioPermissionGranted) {
            audioPermissionGranted = true;
            
            // Tentar reproduzir a música
            if (musicElement && musicElement.readyState >= 2) {
            musicElement.play().then(() => {
                console.log('Música iniciada pelo usuário');
                isMusicPlaying = true;
                updateMusicButton();
                welcomeModal.hide();
                
                // Salvar estado da música
                saveMusicState();
                
                // Verificar se o áudio está realmente tocando (não silenciado)
                setTimeout(() => {
                    simpleAudioCheck();
                }, 1000);
                
                showNotification('🎵 Bem-vindos ao nosso casamento! 💕', 'success');
            }).catch(error => {
                    console.log('Erro ao reproduzir música:', error);
                    welcomeModal.hide();
                    showNotification('Erro ao ativar música. Tente clicar no botão de áudio.', 'warning');
                });
            } else {
                welcomeModal.hide();
                showNotification('Aguarde o áudio carregar e tente clicar no botão de áudio.', 'info');
            }
        }
    }
    
    /**
     * Verifica se o áudio está realmente tocando (não silenciado)
     */
    function checkAudioStatus() {
        if (musicElement && !musicElement.muted && musicElement.volume > 0) {
            try {
                // Verificar se o áudio está realmente produzindo som
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const source = audioContext.createMediaElementSource(musicElement);
                const analyser = audioContext.createAnalyser();
                const gainNode = audioContext.createGain();
                
                source.connect(gainNode);
                gainNode.connect(analyser);
                analyser.connect(audioContext.destination);
                
                analyser.fftSize = 256;
                const dataArray = new Uint8Array(analyser.frequencyBinCount);
                
                // Verificar se há dados de áudio
                setTimeout(() => {
                    analyser.getByteFrequencyData(dataArray);
                    const hasAudio = dataArray.some(value => value > 0);
                    
                    if (!hasAudio) {
                        showAudioMutedWarning();
                    }
                    
                    // Limpar recursos
                    source.disconnect();
                    gainNode.disconnect();
                    analyser.disconnect();
                }, 500);
            } catch (error) {
                console.log('Erro ao verificar status do áudio:', error);
                // Fallback: verificar se o elemento está pausado
                if (musicElement.paused) {
                    showAudioMutedWarning();
                }
            }
        }
    }
    
    /**
     * Mostra aviso se o áudio estiver silenciado
     */
    function showAudioMutedWarning() {
        showNotification('🔇 O som da aba está desativado! Clique no ícone de som na aba do navegador para ativar.', 'warning');
        
        // Mostrar dica mais detalhada após um tempo
        setTimeout(() => {
            showNotification('💡 Dica: Procure pelo ícone 🔊 ou 🔇 na aba do navegador e clique para ativar o som', 'info');
        }, 3000);
    }
    
    /**
     * Verificação simples e confiável do status do áudio
     */
    function simpleAudioCheck() {
        if (musicElement && !musicElement.paused && !musicElement.muted && musicElement.volume > 0) {
            // Verificar se o áudio está realmente tocando usando uma abordagem mais simples
            const currentTime = musicElement.currentTime;
            const duration = musicElement.duration;
            
            // Se o áudio está "tocando" mas o tempo não está avançando, pode estar silenciado
            setTimeout(() => {
                if (musicElement.currentTime === currentTime && musicElement.currentTime > 0) {
                    showAudioMutedWarning();
                }
            }, 2000);
        }
    }
    
    /**
     * Salva o estado da música no localStorage
     */
    function saveMusicState() {
        const musicState = {
            isPlaying: isMusicPlaying,
            currentTime: musicElement ? musicElement.currentTime : 0,
            timestamp: Date.now()
        };
        localStorage.setItem('casamento_music_state', JSON.stringify(musicState));
    }
    
    /**
     * Restaura o estado da música do localStorage
     */
    function restoreMusicState() {
        const savedState = localStorage.getItem('casamento_music_state');
        if (savedState) {
            try {
                const musicState = JSON.parse(savedState);
                const timeDiff = Date.now() - musicState.timestamp;
                
                // Se passou menos de 30 segundos, restaurar o estado
                if (timeDiff < 30000 && musicState.isPlaying) {
                    setTimeout(() => {
                        if (musicElement && musicElement.readyState >= 2) {
                            musicElement.currentTime = musicState.currentTime;
                            musicElement.play().then(() => {
                                isMusicPlaying = true;
                                updateMusicButton();
                                console.log('Música restaurada de outra página');
                            }).catch(error => {
                                console.log('Não foi possível restaurar a música:', error);
                            });
                        }
                    }, 1000);
                }
            } catch (error) {
                console.log('Erro ao restaurar estado da música:', error);
            }
        }
    }
    
    /**
     * Limpa o estado da música do localStorage
     */
    function clearMusicState() {
        localStorage.removeItem('casamento_music_state');
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
        
        // Salvar estado antes de sair da página
        $(window).on('beforeunload', function() {
            saveMusicState();
        });
        
        // Salvar estado periodicamente
        setInterval(saveMusicState, 5000);
    }
    
    /**
     * Configura o áudio de fundo
     */
    function setupAudio() {
        if (musicElement) {
            musicElement.volume = 0.4; // Volume médio
            musicElement.muted = false;
            
            // Event listeners
            musicElement.addEventListener('canplaythrough', function() {
                console.log('Áudio carregado e pronto para reprodução');
            });
            
            musicElement.addEventListener('loadeddata', function() {
                console.log('Dados do áudio carregados');
            });
            
            musicElement.addEventListener('error', function(e) {
                console.warn('Erro ao carregar áudio:', e);
                showNotification('Erro ao carregar música', 'warning');
                updateMusicButton();
            });
            
            musicElement.addEventListener('play', function() {
                isMusicPlaying = true;
                updateMusicButton();
            });
            
            musicElement.addEventListener('pause', function() {
                isMusicPlaying = false;
                updateMusicButton();
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
        return new Promise((resolve, reject) => {
            if (!musicElement) {
                reject(new Error('Elemento de áudio não encontrado'));
                return;
            }
            
            // Forçar permissão de áudio
            if (musicElement.paused) {
                musicElement.play().then(() => {
                    isMusicPlaying = true;
                    updateMusicButton();
                    showNotification('🎵 Música iniciada', 'success');
                    
                    // Salvar estado da música
                    saveMusicState();
                    
                    // Verificar se o áudio está realmente tocando
                    setTimeout(() => {
                        simpleAudioCheck();
                    }, 1000);
                    
                    resolve();
                }).catch(error => {
                    console.warn('Erro ao reproduzir música:', error);
                    showNotification('Não foi possível reproduzir a música. Tente clicar novamente.', 'warning');
                    reject(error);
                });
            } else {
                resolve();
            }
        });
    }
    
    /**
     * Pausa a música
     */
    function pauseMusic() {
        if (musicElement && !musicElement.paused) {
            musicElement.pause();
            isMusicPlaying = false;
            updateMusicButton();
            showNotification('⏸️ Música pausada', 'info');
            
            // Salvar estado da música
            saveMusicState();
        }
    }
    
    /**
     * Atualiza o texto do botão de música
     */
    function updateMusicButton() {
        const button = $('#musicToggleBtn');
        const text = $('#musicToggleText');
        
        if (isMusicPlaying) {
            text.text('Pausar Áudio');
            button.removeClass('btn-outline-secondary').addClass('btn-outline-danger');
            button.find('i').removeClass('fa-music').addClass('fa-pause');
        } else {
            text.text('Reproduzir Áudio');
            button.removeClass('btn-outline-danger').addClass('btn-outline-secondary');
            button.find('i').removeClass('fa-pause').addClass('fa-music');
        }
    }
    
    /**
     * Exibe a lista de presentes (redireciona para lista.php)
     */
    function viewGifts() {
        showLoading(true);
        
        // Simular carregamento
        setTimeout(() => {
            showLoading(false);
            showNotification('Carregando lista de presentes...', 'info');
            
            // Redirecionar para lista de presentes
            setTimeout(() => {
                window.location.href = 'lista.php';
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
        // A mensagem de boas-vindas agora é mostrada no modal
        // Esta função é mantida para compatibilidade mas não faz nada
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
