/**
 * Controlador de música para manter continuidade entre páginas
 */

$(document).ready(function() {
    'use strict';
    
    let musicElement = null;
    let isMusicPlaying = false;
    
    // Aguardar um pouco para garantir que o elemento esteja carregado
    setTimeout(() => {
        musicElement = document.getElementById('backgroundMusic');
        
        if (musicElement) {
            initMusicController();
        } else {
            // Tentar novamente após mais tempo
            setTimeout(() => {
                musicElement = document.getElementById('backgroundMusic');
                if (musicElement) {
                    initMusicController();
                }
            }, 1000);
        }
    }, 500);
    
    /**
     * Inicializa o controlador de música
     */
    function initMusicController() {
        if (!musicElement) {
            return;
        }
        
        musicElement.volume = 0.4;
        musicElement.muted = false;
        
        // Event listeners
        musicElement.addEventListener('play', function() {
            isMusicPlaying = true;
            updateMusicButton();
            
            // Verificar se realmente está tocando após um delay
            setTimeout(() => {
                if (musicElement.paused) {
                    musicElement.play().then(() => {
                        // Música forçada a tocar novamente
                    }).catch(error => {
                        // Erro ao forçar música
                    });
                }
            }, 100);
        });
        
        // Remover listener de pause que estava causando problemas
        // musicElement.addEventListener('pause', function() {
        //     console.log('⏸️ Evento PAUSE disparado');
        //     isMusicPlaying = false;
        //     updateMusicButton();
        // });
        
        musicElement.addEventListener('error', function(e) {
            console.log('❌ Erro no elemento de áudio:', e);
        });
        
        // Não restaurar estado - sempre reiniciar música
        
        // Verificar se deve iniciar música automaticamente (se há sessão ativa)
        setTimeout(() => {
            if (shouldStartMusicFromSession()) {
                // Forçar música imediatamente quando há sessão
                forcePlayMusicWithSession();
            } else {
                
                // SEMPRE verificar se música está realmente tocando
                setTimeout(() => {
                    if (musicElement.paused || musicElement.currentTime === 0) {
                        musicElement.currentTime = 0;
                        musicElement.play().then(() => {
                            isMusicPlaying = true;
                            updateMusicButton();
                        }).catch(error => {
                            // Configurar interação do usuário
                            setupUserInteractionMusicStart();
                        });
                    } else {
                    }
                }, 500);
            }
        }, 300);
        
        // Salvar estado periodicamente
        setInterval(saveMusicState, 5000);
        
        // Salvar antes de sair
        $(window).on('beforeunload', function() {
            saveMusicState();
        });
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
                
                // Se passou menos de 30 segundos, restaurar apenas a posição
                if (timeDiff < 30000 && musicState.isPlaying) {
                    setTimeout(() => {
                        if (musicElement && musicElement.readyState >= 2) {
                            musicElement.currentTime = musicState.currentTime;
                            isMusicPlaying = false; // Não reproduzir automaticamente
                            updateMusicButton();
                        }
                    }, 500);
                }
            } catch (error) {
                // Erro silencioso
            }
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
     * Atualiza o botão de música se existir
     */
    function updateMusicButton() {
        const button = $('#musicToggleBtn');
        const text = $('#musicToggleText');
        
        if (button.length && text.length) {
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
     * Reproduz a música (sempre reinicia do início)
     */
    function playMusic() {
        if (!musicElement) {
            return;
        }
        
        console.log('🎵 playMusic() chamado, estado atual:', {
            paused: musicElement.paused,
            currentTime: musicElement.currentTime
        });
        
        // Se já está tocando, não fazer nada
        if (!musicElement.paused && isMusicPlaying) {
            return;
        }
        
        // FORÇAR reinício da música do início
        musicElement.currentTime = 0;
        musicElement.load();
        
        // Tentar tocar automaticamente
        setTimeout(() => {
            musicElement.play().then(() => {
                isMusicPlaying = true;
                updateMusicButton();
                saveMusicState();
            }).catch(error => {
                // Configurar para tocar na primeira interação
                setupUserInteractionMusicStart();
            });
        }, 100);
    }
    
    /**
     * Pausa a música
     */
    function pauseMusic() {
        if (musicElement && !musicElement.paused) {
            musicElement.pause();
            isMusicPlaying = false;
            updateMusicButton();
            saveMusicState();
        }
    }
    
    // Event listener para o botão de música
    $(document).on('click', '#musicToggleBtn', function() {
        toggleMusic();
    });
    
    /**
     * Verifica se deve iniciar música baseado na sessão
     */
    function shouldStartMusicFromSession() {
        const sessionData = localStorage.getItem('casamento_welcome_session');
        
        if (!sessionData) {
            return false;
        }
        
        try {
            const session = JSON.parse(sessionData);
            const now = Date.now();
            const oneDayInMs = 24 * 60 * 60 * 1000;
            
            if (now - session.timestamp < oneDayInMs) {
                return true;
            } else {
                localStorage.removeItem('casamento_welcome_session');
                return false;
            }
        } catch (error) {
            localStorage.removeItem('casamento_welcome_session');
            return false;
        }
    }
    
    /**
     * Força música a tocar quando há sessão ativa (simula clique)
     */
    function forcePlayMusicWithSession() {
        if (!musicElement) {
            return;
        }
        
        // Verificar se estamos na página inicial (index.php)
        const isIndexPage = window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/') || window.location.pathname === '';
        
        if (isIndexPage) {
            // Na página inicial, mostrar mensagem de boas-vindas
            showWelcomeBackMessage();
        } else {
            // Em outras páginas (como lista.php), mostrar modal de presentes
            showGiftsWelcomeMessage();
        }
    }
    
    /**
     * Mostra mensagem de boas-vindas quando há sessão ativa
     */
    function showWelcomeBackMessage() {
        // Criar modal de boas-vindas
        const welcomeModal = document.createElement('div');
        welcomeModal.id = 'welcomeBackModal';
        welcomeModal.innerHTML = `
            <div style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Inter', sans-serif;
            ">
                <div style="
                    background: white;
                    border-radius: 20px;
                    padding: 40px;
                    max-width: 500px;
                    width: 90%;
                    text-align: center;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                    animation: fadeInUp 0.5s ease-out;
                ">
                    <h2 style="
                        color: #333;
                        margin-bottom: 20px;
                        font-size: 28px;
                        font-weight: 600;
                    ">Que bom ter você de volta!</h2>
                    
                    <p style="
                        color: #666;
                        line-height: 1.6;
                        margin-bottom: 30px;
                        font-size: 16px;
                    ">
                        Continue navegando, deixe um recado aos noivos e aproveite cada momento desta celebração especial! 💕
                    </p>
                    
                    <button id="continueMusicBtn" style="
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        border: none;
                        padding: 15px 40px;
                        border-radius: 50px;
                        font-size: 18px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: transform 0.2s, box-shadow 0.2s;
                        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 25px rgba(102, 126, 234, 0.4)'" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.3)'">
                        Continuar
                    </button>
                </div>
            </div>
            
            <style>
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                @keyframes bounce {
                    0%, 20%, 50%, 80%, 100% {
                        transform: translateY(0);
                    }
                    40% {
                        transform: translateY(-10px);
                    }
                    60% {
                        transform: translateY(-5px);
                    }
                }
            </style>
        `;
        
        // Adicionar ao body
        document.body.appendChild(welcomeModal);
        
        // Event listener para o botão
        document.getElementById('continueMusicBtn').addEventListener('click', function() {
            // Remover modal
            welcomeModal.remove();
            
            // Iniciar música
            startMusicFromWelcome();
        });
        
        // Fechar modal ao clicar fora (opcional)
        welcomeModal.addEventListener('click', function(e) {
            if (e.target === welcomeModal) {
                welcomeModal.remove();
                startMusicFromWelcome();
            }
        });
    }
    
    /**
     * Inicia música após mensagem de boas-vindas
     */
    function startMusicFromWelcome() {
        if (!musicElement) return;
        
        // Resetar música
        musicElement.currentTime = 0;
        musicElement.volume = 0.4;
        musicElement.muted = false;
        
        // Tentar tocar
        musicElement.play().then(() => {
            isMusicPlaying = true;
            updateMusicButton();
            saveMusicState();
        }).catch(error => {
            // Erro ao iniciar música
        });
    }
    
    /**
     * Mostra mensagem de boas-vindas para página de presentes
     */
    function showGiftsWelcomeMessage() {
        const giftsModal = document.createElement('div');
        giftsModal.id = 'giftsWelcomeModal';
        giftsModal.innerHTML = `
            <div style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Inter', sans-serif;
            ">
                <div style="
                    background: white;
                    border-radius: 20px;
                    padding: 40px;
                    max-width: 500px;
                    width: 90%;
                    text-align: center;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                    animation: fadeInUp 0.5s ease-out;
                ">
                    <div style="
                        font-size: 60px;
                        margin-bottom: 20px;
                        animation: bounce 2s infinite;
                    ">🎁</div>
                    
                    <h2 style="
                        color: #333;
                        margin-bottom: 20px;
                        font-size: 28px;
                        font-weight: 600;
                    ">Bem-vindo aos presentes!</h2>
                    
                    <p style="
                        color: #666;
                        line-height: 1.6;
                        margin-bottom: 30px;
                        font-size: 16px;
                    ">
                        Escolha com carinho os presentes que mais combinam com os noivos e contribua para tornar este dia ainda mais especial! 💕
                    </p>
                    
                    <button id="continueGiftsBtn" style="
                        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
                        color: white;
                        border: none;
                        padding: 15px 40px;
                        border-radius: 50px;
                        font-size: 18px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: transform 0.2s, box-shadow 0.2s;
                        box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 25px rgba(255, 107, 107, 0.4)'" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(255, 107, 107, 0.3)'">
                        Continuar
                    </button>
                </div>
            </div>
            
            <style>
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                @keyframes bounce {
                    0%, 20%, 50%, 80%, 100% {
                        transform: translateY(0);
                    }
                    40% {
                        transform: translateY(-10px);
                    }
                    60% {
                        transform: translateY(-5px);
                    }
                }
            </style>
        `;
        document.body.appendChild(giftsModal);
        document.getElementById('continueGiftsBtn').addEventListener('click', function() {
            giftsModal.remove();
            startMusicFromGifts();
        });
        giftsModal.addEventListener('click', function(e) {
            if (e.target === giftsModal) {
                giftsModal.remove();
                startMusicFromGifts();
            }
        });
    }
    
    /**
     * Inicia música quando usuário clica no botão continuar do modal de presentes
     */
    function startMusicFromGifts() {
        if (!musicElement) return;
        
        // Resetar música
        musicElement.currentTime = 0;
        musicElement.volume = 0.4;
        musicElement.muted = false;
        
        // Tentar tocar
        musicElement.play().then(() => {
            isMusicPlaying = true;
            updateMusicButton();
            saveMusicState();
        }).catch(error => {
            // Erro ao iniciar música
        });
    }

    /**
     * Cria botão invisível e simula clique real do usuário
     */
    function createInvisibleButtonAndClick() {
        
        // Criar botão invisível
        const invisibleButton = document.createElement('button');
        invisibleButton.id = 'invisible-music-button';
        invisibleButton.style.cssText = `
            position: absolute;
            top: -9999px;
            left: -9999px;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
            z-index: -1;
        `;
        invisibleButton.textContent = 'Play Music';
        
        // Adicionar ao body
        document.body.appendChild(invisibleButton);
        
        // Adicionar event listener
        invisibleButton.addEventListener('click', function() {
            startMusicFromInvisibleButton();
        });
        
        // Simular clique após um pequeno delay
        setTimeout(() => {
            
            // Criar evento de clique real
            const clickEvent = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window,
                button: 0,
                buttons: 1,
                clientX: 0,
                clientY: 0
            });
            
            // Disparar evento
            invisibleButton.dispatchEvent(clickEvent);
            
            // Remover botão após uso
            setTimeout(() => {
                if (invisibleButton.parentNode) {
                    invisibleButton.parentNode.removeChild(invisibleButton);
                }
            }, 500);
            
        }, 200);
    }
    
    /**
     * Inicia música a partir do botão invisível
     */
    function startMusicFromInvisibleButton() {
        if (!musicElement) return;
        
        
        // Resetar música
        musicElement.currentTime = 0;
        musicElement.volume = 0.4;
        musicElement.muted = false;
        
        // Tentar tocar
                musicElement.play().then(() => {
                    
                    isMusicPlaying = true;
                    updateMusicButton();
                    saveMusicState();
                }).catch(error => {
                    // Fallback para interação normal
                    setupUserInteractionMusicStart();
                });
    }
    
    /**
     * Inicia música quando há sessão ativa
     */
    function startMusicFromSession() {
        if (!musicElement) {
            return;
        }
        
        // Verificar se estamos na página inicial
        const isIndexPage = window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/') || window.location.pathname === '';
        
        if (isIndexPage) {
            // Na página inicial, configurar para interação do usuário
            setupUserInteractionMusicStart();
        } else {
            // Em outras páginas, mostrar modal de presentes
            showGiftsWelcomeMessage();
        }
    }
    
    /**
     * Tenta tocar música automaticamente quando há sessão
     */
    function tryAutoplayFromSession() {
        if (!musicElement) {
            return;
        }
        
        // Usar estratégia agressiva primeiro
        tryAggressiveAutoplay();
    }
    
    /**
     * Tenta autoplay com diferentes estratégias
     */
    function attemptAutoplay(attempt = 1) {
        if (!musicElement) return;
        
        const delays = [100, 300, 500, 1000]; // Diferentes delays
        const delay = delays[Math.min(attempt - 1, delays.length - 1)];
        
        setTimeout(() => {
            let playPromise;
            
            // Tentar diferentes abordagens
            if (attempt === 1) {
                // Primeira tentativa: básica
                playPromise = musicElement.play();
            } else if (attempt === 2) {
                // Segunda tentativa: com muted/unmuted
                musicElement.muted = true;
                playPromise = musicElement.play().then(() => {
                    musicElement.muted = false;
                });
            } else if (attempt === 3) {
                // Terceira tentativa: com volume baixo
                musicElement.volume = 0.1;
                playPromise = musicElement.play().then(() => {
                    musicElement.volume = 0.4;
                });
            } else {
                // Quarta tentativa: última chance
                playPromise = musicElement.play();
            }
            
            // Processar resultado
            if (playPromise) {
                playPromise.then(() => {
                    console.log(`Música iniciada automaticamente (tentativa ${attempt})`);
                    showNotification('🎵 Música reiniciada automaticamente! 💕', 'success');
                    
                    isMusicPlaying = true;
                    updateMusicButton();
                    saveMusicState();
                }).catch(error => {
                    console.log(`Autoplay falhou na tentativa ${attempt}:`, error);
                    
                    if (attempt < 4) {
                        // Tentar próxima estratégia
                        attemptAutoplay(attempt + 1);
                    } else {
                        // Todas as tentativas falharam
                        console.log('Todas as tentativas de autoplay falharam, aguardando interação do usuário');
                        showNotification('🎵 Clique em qualquer lugar para reiniciar a música... 💕', 'info');
                    }
                });
            }
            
        }, delay);
    }
    
    /**
     * Configura para iniciar música após interação do usuário
     */
    function setupUserInteractionMusicStart() {
        if (!musicElement) {
            return;
        }
        
        let musicStarted = false;
        
        const startMusic = () => {
            if (musicStarted) return;
            musicStarted = true;
            
            // Resetar música
            musicElement.currentTime = 0;
            
            // Tentar tocar
            const playPromise = musicElement.play();
            
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    
                    isMusicPlaying = true;
                    updateMusicButton();
                    saveMusicState();
                    
                    // Remover listeners
                    document.removeEventListener('click', startMusic);
                    document.removeEventListener('touchstart', startMusic);
                    document.removeEventListener('keydown', startMusic);
                }).catch(error => {
                    musicStarted = false; // Permitir nova tentativa
                });
            }
        };
        
        // Adicionar listeners simples
        document.addEventListener('click', startMusic, { once: true });
        document.addEventListener('touchstart', startMusic, { once: true });
        document.addEventListener('keydown', startMusic, { once: true });
    
    }
    
    /**
     * Função auxiliar para mostrar notificações (compatibilidade)
     */
    function showNotification(message, type = 'info') {
        // Se MainController estiver disponível, usar ele
        if (window.MainController && window.MainController.showNotification) {
            window.MainController.showNotification(message, type);
            return;
        }
    }

    /**
     * Função de debug para forçar reinício da música
     */
    function forceRestartMusic() {
        if (!musicElement) {
            return;
        }
        
        // Forçar reinício
        musicElement.currentTime = 0;
        musicElement.load();
        
        // Tentar múltiplas estratégias
        attemptAutoplay(1);
    }
    
    /**
     * Função para tentar autoplay agressivo
     */
    function tryAggressiveAutoplay() {
        if (!musicElement) return;
        
        // Estratégia 1: Muted primeiro
        musicElement.muted = true;
        musicElement.currentTime = 0;
        musicElement.load();
        
        setTimeout(() => {
            musicElement.play().then(() => {
                musicElement.muted = false;
                isMusicPlaying = true;
                updateMusicButton();
                saveMusicState();
                showNotification('🎵 Música iniciada automaticamente! 💕', 'success');
            }).catch(() => {
                // Estratégia 2: Volume baixo
                musicElement.volume = 0.01;
                musicElement.play().then(() => {
                    musicElement.volume = 0.4;
                    isMusicPlaying = true;
                    updateMusicButton();
                    saveMusicState();
                    showNotification('🎵 Música iniciada automaticamente! 💕', 'success');
                }).catch(() => {
                    showNotification('🎵 Clique em qualquer lugar para reiniciar a música... 💕', 'info');
                });
            });
        }, 100);
    }
    
    /**
     * Função de debug para verificar status
     */
    function debugMusicStatus() {
        const sessionData = localStorage.getItem('casamento_welcome_session');
        
        return {
            hasElement: !!musicElement,
            isPaused: musicElement ? musicElement.paused : null,
            currentTime: musicElement ? musicElement.currentTime : null,
            hasSession: !!sessionData,
            hasController: !!window.MusicController
        };
    }

    /**
     * Função simples para testar reprodução
     */
    function testPlay() {
        if (!musicElement) {
            return;
        }
        
        musicElement.currentTime = 0;
        musicElement.play().then(() => {
            isMusicPlaying = true;
            updateMusicButton();
        }).catch(error => {
            console.log('❌ Erro ao tocar:', error);
        });
    }
    
    /**
     * Função para forçar música a tocar (teste direto)
     */
    function forcePlay() {
        
        // Procurar elemento novamente
        musicElement = document.getElementById('backgroundMusic');
        
        if (!musicElement) {
            return;
        }

        // Resetar
        musicElement.currentTime = 0;
        musicElement.volume = 0.4;
        musicElement.muted = false;
        
        // Tentar tocar
        musicElement.play().then(() => {
            isMusicPlaying = true;
            updateMusicButton();
            showNotification('🎵 Música forçada a tocar! 💕', 'success');
        }).catch(error => {
            showNotification('❌ Falha ao tocar música', 'error');
        });
    }

    /**
     * Função de teste para simular clique invisível
     */
    function testInvisibleClick() {
        createInvisibleButtonAndClick();
    }
    
    /**
     * Força música a tocar SEMPRE (última tentativa)
     */
    function forceMusicAlways() {
        if (!musicElement) {
            return;
        }
        
        // Resetar completamente
        musicElement.currentTime = 0;
        musicElement.volume = 0.4;
        musicElement.muted = false;
        musicElement.load();
        
        // Múltiplas tentativas
        const attempts = [100, 300, 500, 1000];
        
        attempts.forEach((delay, index) => {
            setTimeout(() => {
                
                musicElement.play().then(() => {
                    isMusicPlaying = true;
                    updateMusicButton();
                }).catch(error => {
                    
                    if (index === attempts.length - 1) {
                        setupUserInteractionMusicStart();
                    }
                });
            }, delay);
        });
    }
    
    /**
     * Força música a tocar se estiver pausada
     */
    function forcePlayIfPaused() {
        if (!musicElement) {
            return;
        }
        
        if (musicElement.paused) {
            musicElement.play().then(() => {
                isMusicPlaying = true;
                updateMusicButton();
            }).catch(error => {
                console.log('❌ Erro ao forçar música:', error);
            });
        }
    }
    
    /**
     * Verifica se a música está realmente tocando
     */
    function checkIfMusicIsActuallyPlaying() {
        if (!musicElement) return false;
        
        const isActuallyPlaying = !musicElement.paused && musicElement.currentTime > 0;
        
        if (!isActuallyPlaying && isMusicPlaying) {
            
            musicElement.currentTime = 0;
            musicElement.play().then(() => {
                console.log('✅ Música corrigida e tocando!');
            }).catch(error => {
                console.log('❌ Erro ao corrigir música:', error);
            });
        }
        
        return isActuallyPlaying;
    }

    // Expor funções globalmente
    window.MusicController = {
        toggleMusic: toggleMusic,
        playMusic: playMusic,
        pauseMusic: pauseMusic,
        shouldStartMusicFromSession: shouldStartMusicFromSession,
        startMusicFromSession: startMusicFromSession,
        forcePlayMusicWithSession: forcePlayMusicWithSession,
        showWelcomeBackMessage: showWelcomeBackMessage,
        startMusicFromWelcome: startMusicFromWelcome,
        showGiftsWelcomeMessage: showGiftsWelcomeMessage,
        startMusicFromGifts: startMusicFromGifts,
        createInvisibleButtonAndClick: createInvisibleButtonAndClick,
        testInvisibleClick: testInvisibleClick,
        forceMusicAlways: forceMusicAlways,
        forcePlayIfPaused: forcePlayIfPaused,
        checkIfMusicIsActuallyPlaying: checkIfMusicIsActuallyPlaying,
        forceRestartMusic: forceRestartMusic,
        debugMusicStatus: debugMusicStatus,
        tryAggressiveAutoplay: tryAggressiveAutoplay,
        testPlay: testPlay,
        forcePlay: forcePlay
    };
});
