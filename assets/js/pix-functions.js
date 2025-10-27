/**
 * Funções PIX simplificadas para checkout de presentes
 * Modal simplificado com chave PIX e WhatsApp
 */

// Variáveis globais para checkout
let checkoutModal = null;
let currentPixTransactionId = null; // ID da transação PIX atual

/**
 * Redireciona para a página de checkout
 */
window.redirectToCheckout = function(button, returnUrl) {
    const giftId = button.getAttribute('data-gift-id');
    const url = `checkout.php?gift_id=${giftId}&return_url=${encodeURIComponent(returnUrl)}`;
    window.location.href = url;
}

/**
 * Gera link do WhatsApp com mensagem pré-formatada
 */
function generateWhatsAppLink(giftName, giftValue) {
    let formattedValue = 'R$ 0,00';
    if (giftValue && giftValue !== '' && giftValue !== '0') {
        let cleanValue = giftValue.toString().replace(/[^\d,.-]/g, '').replace(',', '.');
        const numericValue = parseFloat(cleanValue);
        if (!isNaN(numericValue) && numericValue > 0) {
            formattedValue = 'R$ ' + numericValue.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }
    
    const donorName = document.getElementById('donorName').value.trim() || '[SEU NOME]';
    
    const message = `Olá Marislan! 👋

Acabei de fazer um PIX para o presente "${giftName}" no valor de ${formattedValue}.

Aqui está o comprovante do pagamento:

[ANEXAR COMPROVANTE AQUI]

Muito obrigado(a) pelo convite! 
Que vocês sejam muito felizes! 💕

Atenciosamente,
${donorName}`;

    const whatsappNumber = '5511996271186'; // Número da Marislan
    const encodedMessage = encodeURIComponent(message);
    const whatsappLink = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;
    
    // Atualizar link do WhatsApp
    const whatsappLinkEl = document.getElementById('whatsappLink');
    if (whatsappLinkEl) {
        whatsappLinkEl.href = whatsappLink;
    }
}

/**
 * Confirma o envio do presente
 */
window.confirmGift = function() {
    if (!window.checkoutData) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro: Dados do presente não encontrados', 'error');
        }
        return;
    }
    
    const donorNameEl = document.getElementById('donorName');
    if (!donorNameEl) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro: Campo de nome não encontrado', 'error');
        }
        return;
    }
    
    const donorName = donorNameEl.value.trim();
    const donorPhoneEl = document.getElementById('donorPhone');
    const donorPhone = donorPhoneEl ? donorPhoneEl.value.trim() : '';
    
    if (!donorName) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Por favor, informe seu nome no Passo 1 antes de confirmar', 'warning');
        }
        document.getElementById('donorName').focus();
        // Destacar o campo
        document.getElementById('donorName').classList.add('is-invalid');
        setTimeout(() => {
            document.getElementById('donorName').classList.remove('is-invalid');
        }, 3000);
        return;
    }
    
    // Desabilitar botão e mostrar loading
    const btn = document.getElementById('confirmGiftBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
    
    // Preparar dados
    const cleanValue = window.checkoutData.giftValue.toString().replace(/[^\d,.-]/g, '').replace(',', '.');
    const numericValue = parseFloat(cleanValue);
    
    if (numericValue <= 0) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Valor inválido para PIX', 'error');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-gift me-2"></i>Confirmar Envio do Presente';
        return;
    }
    
    // Verificar se já existe transação PIX
    if (currentPixTransactionId) {
        // Atualizar status da transação existente
        updatePixTransactionStatus(currentPixTransactionId, 'pre_confirmado');
    } else {
        // Criar nova transação PIX
        createPixTransaction(numericValue, donorName, donorPhone, 'pre_confirmado');
    }
}

/**
 * Atualiza status de uma transação PIX existente
 */
function updatePixTransactionStatus(transactionId, newStatus) {
    const btn = document.getElementById('confirmGiftBtn');
    
    fetch(window.BASE_URL + '/api/update_pix_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            transaction_id: transactionId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof showNotification !== 'undefined') {
                showNotification('Status atualizado! Aguarde a confirmação final.', 'success');
            }
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Presente Confirmado!';
            
            // Mostrar modal de agradecimento
            setTimeout(() => {
                if (window.thankYouModal) {
                    window.thankYouModal.show();
                }
            }, 1000);
        } else {
            throw new Error(data.message || 'Erro ao atualizar status');
        }
    })
    .catch(error => {
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro ao confirmar presente: ' + error.message, 'error');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-gift me-2"></i>Confirmar Envio do Presente';
    });
}

/**
 * Cria nova transação PIX
 */
function createPixTransaction(amount, donorName, donorPhone, status) {
    const btn = document.getElementById('confirmGiftBtn');
    
    fetch(window.BASE_URL + '/api/save_pix_transaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            gift_id: window.checkoutData.giftId,
            gift_name: window.checkoutData.giftName,
            amount: amount,
            donor_name: donorName,
            donor_phone: donorPhone,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Armazenar ID da transação
            currentPixTransactionId = data.transaction_id;
            
            if (typeof showNotification !== 'undefined') {
                showNotification('Presente confirmado! Aguarde a confirmação final.', 'success');
            }
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Presente Confirmado!';
            
            // Mostrar modal de agradecimento
            setTimeout(() => {
                if (window.thankYouModal) {
                    window.thankYouModal.show();
                }
            }, 1000);
        } else {
            throw new Error(data.message || 'Erro ao salvar presente');
        }
    })
    .catch(error => {
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro ao confirmar presente: ' + error.message, 'error');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-gift me-2"></i>Confirmar Envio do Presente';
    });
}

/**
 * Copia chave PIX para área de transferência e salva como iniciado
 */
window.copyPixKey = function() {
    const pixKeyEl = document.getElementById('pixKeyDisplay');
    const pixKey = pixKeyEl ? pixKeyEl.value : '';
    
    if (!pixKey) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Chave PIX não encontrada', 'warning');
        }
        return;
    }
    
    navigator.clipboard.writeText(pixKey).then(function() {
        if (typeof showNotification !== 'undefined') {
            showNotification('Chave PIX copiada!', 'success');
        }
        
        // Salvar como iniciado se ainda não foi salvo
        savePixAsInitiated();
    }).catch(function(err) {
        // Fallback: selecionar texto
        if (pixKeyEl) {
            pixKeyEl.select();
            document.execCommand('copy');
        }
        if (typeof showNotification !== 'undefined') {
            showNotification('Chave PIX selecionada!', 'info');
        }
        
        // Salvar como iniciado mesmo no fallback
        savePixAsInitiated();
    });
}

/**
 * Salva transação PIX como iniciado
 */
function savePixAsInitiated() {
    if (!window.checkoutData) {
        return;
    }
    
    const donorNameEl = document.getElementById('donorName');
    if (!donorNameEl) {
        console.log('Campo donorName não encontrado');
        return;
    }
    
    const donorName = donorNameEl.value.trim();
    const donorPhoneEl = document.getElementById('donorPhone');
    const donorPhone = donorPhoneEl ? donorPhoneEl.value.trim() : '';
    
    // Verificar se nome foi preenchido
    if (!donorName) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Por favor, informe seu nome no Passo 1 antes de copiar a chave PIX', 'warning');
        }
        document.getElementById('donorName').focus();
        // Destacar o campo
        document.getElementById('donorName').classList.add('is-invalid');
        setTimeout(() => {
            document.getElementById('donorName').classList.remove('is-invalid');
        }, 3000);
        return;
    }
    
    // Preparar dados
    const cleanValue = window.checkoutData.giftValue.toString().replace(/[^\d,.-]/g, '').replace(',', '.');
    const numericValue = parseFloat(cleanValue);
    
    if (numericValue <= 0) {
        return;
    }
    
    // Salvar transação PIX como iniciado
    fetch(window.BASE_URL + '/api/save_pix_transaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            gift_id: window.checkoutData.giftId,
            gift_name: window.checkoutData.giftName,
            amount: numericValue,
            donor_name: donorName,
            donor_phone: donorPhone,
            status: 'iniciado'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('PIX salvo como iniciado:', data);
            // Armazenar ID da transação para futuras atualizações
            currentPixTransactionId = data.transaction_id;
            if (typeof showNotification !== 'undefined') {
                showNotification('Presente iniciado! Agora você pode fazer o PIX.', 'info');
            }
        }
    })
    .catch(error => {
        console.log('Erro ao salvar PIX como iniciado:', error);
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro ao salvar presente. Tente novamente.', 'error');
        }
    });
}

/**
 * Atualiza link do WhatsApp quando o nome for digitado
 */
document.addEventListener('DOMContentLoaded', function() {
    const donorNameEl = document.getElementById('donorName');
    if (donorNameEl) {
        donorNameEl.addEventListener('input', function() {
            if (window.checkoutData) {
                generateWhatsAppLink(window.checkoutData.giftName, window.checkoutData.giftValue);
            }
        });
    }
    
    // Máscara para telefone
    const donorPhoneEl = document.getElementById('donorPhone');
    if (donorPhoneEl) {
        donorPhoneEl.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                if (value.length < 14) {
                    value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                }
            }
            this.value = value;
        });
    }
});

/**
 * Função de compatibilidade (não usada mais)
 */
window.generateCheckout = function() {
    // Não faz nada no modal simplificado
    console.log('Modal simplificado - não precisa gerar PIX');
}

/**
 * Função de compatibilidade (não usada mais)
 */
window.refreshQRCode = function() {
    // Não faz nada no modal simplificado
    console.log('Modal simplificado - não precisa atualizar QR Code');
}

/**
 * Função de compatibilidade (não usada mais)
 */
window.copyPixCode = function() {
    // Não faz nada no modal simplificado
    console.log('Modal simplificado - não precisa copiar código PIX');
}