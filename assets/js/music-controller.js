/**
 * Controlador de música para manter continuidade entre páginas
 */

$(document).ready(function() {
    'use strict';
    
    let musicElement = document.getElementById('backgroundMusic');
    let isMusicPlaying = false;
    
    // Inicializar música se estiver disponível
    if (musicElement) {
        initMusicController();
    }
    
    /**
     * Inicializa o controlador de música
     */
    function initMusicController() {
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
        
        // Restaurar estado da música
        restoreMusicState();
        
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
                
                // Se passou menos de 30 segundos, restaurar o estado
                if (timeDiff < 30000 && musicState.isPlaying) {
                    setTimeout(() => {
                        if (musicElement && musicElement.readyState >= 2) {
                            musicElement.currentTime = musicState.currentTime;
                            musicElement.play().then(() => {
                                isMusicPlaying = true;
                                updateMusicButton();
                                console.log('Música restaurada na lista de presentes');
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
        if (musicElement && musicElement.paused) {
            musicElement.play().then(() => {
                isMusicPlaying = true;
                updateMusicButton();
                saveMusicState();
            }).catch(error => {
                console.warn('Erro ao reproduzir música:', error);
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
