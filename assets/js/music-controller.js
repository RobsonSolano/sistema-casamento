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
        });
        
        musicElement.addEventListener('pause', function() {
            isMusicPlaying = false;
            updateMusicButton();
        });
        
        musicElement.addEventListener('error', function(e) {
            // Erro silencioso - não mostrar para o usuário
        });
        
        // Restaurar estado da música
        restoreMusicState();
        
        // Não tentar iniciar música automaticamente (bloqueado pelos navegadores)
        // A música será iniciada apenas quando o usuário clicar no botão
        
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
                    }, 1000);
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
     * Reproduz a música
     */
    function playMusic() {
        if (!musicElement) {
            return;
        }
        
        if (musicElement.paused) {
            musicElement.play().then(() => {
                isMusicPlaying = true;
                updateMusicButton();
                saveMusicState();
            }).catch(error => {
                // Erro silencioso - não mostrar para o usuário
            });
        }
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
    
    // Expor funções globalmente
    window.MusicController = {
        toggleMusic: toggleMusic,
        playMusic: playMusic,
        pauseMusic: pauseMusic
    };
});
