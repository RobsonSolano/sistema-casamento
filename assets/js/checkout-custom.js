/**
 * Funções customizadas para o checkout
 */

let pixInitiated = false;
let currentCheckoutTransactionId = null;

// Usar a flag global de pix-functions.js (não redeclarar)
if (typeof window.pixAsInitiatedSaved === 'undefined') {
    window.pixAsInitiatedSaved = false;
}

/**
 * Exibe o QR Code (requer nome preenchido)
 */
window.showQRCode = function() {
    const donorName = document.getElementById('donorName').value.trim();
    
    if (!donorName) {
        showNotification('Por favor, informe seu nome no Passo 1 antes de exibir o QR Code', 'warning');
        const nameField = document.getElementById('donorName');
        nameField.focus();
        nameField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        nameField.classList.add('is-invalid');
        setTimeout(() => {
            nameField.classList.remove('is-invalid');
        }, 3000);
        return;
    }
    
    // Salvar como iniciado no checkout
    savePixAsInitiatedInCheckout(donorName);
    
    // Exibir QR Code e ocultar botão
    const qrCodeButton = document.getElementById('qrCodeButton');
    const qrCodeContainer = document.getElementById('qrCodeContainer');
    
    if (qrCodeButton && qrCodeContainer) {
        qrCodeButton.style.display = 'none';
        qrCodeContainer.style.display = 'block';
    }
}

/**
 * Salva transação PIX como iniciado no checkout (versão simplificada)
 */
function savePixAsInitiatedInCheckout(donorName) {
    // Verificar se já foi salvo (evita duplicação)
    if (window.pixAsInitiatedSaved || pixInitiated) {
        console.log('PIX já foi salvo como iniciado anteriormente');
        return; // Já salvou uma vez
    }
    
    if (!window.checkoutData) {
        return;
    }
    
    const donorPhone = document.getElementById('donorPhone')?.value.trim() || '';
    
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
            pixInitiated = true;
            window.pixAsInitiatedSaved = true; // Sincronizar com flag global
            currentCheckoutTransactionId = data.transaction_id;
        }
    })
    .catch(error => {
        console.log('Erro ao salvar PIX como iniciado:', error);
    });
}

// Aguardar carregamento para sobrescrever funções
$(document).ready(function() {
    // Sobrescrever copyPixCode para exigir nome
    const originalCopyPixCode = window.copyPixCode;
    window.copyPixCode = function() {
        const donorName = document.getElementById('donorName').value.trim();
        
        if (!donorName) {
            showNotification('Por favor, informe seu nome no Passo 1 antes de copiar o código PIX', 'warning');
            const nameField = document.getElementById('donorName');
            nameField.focus();
            nameField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            nameField.classList.add('is-invalid');
            setTimeout(() => {
                nameField.classList.remove('is-invalid');
            }, 3000);
            return;
        }
        
        // Salvar como iniciado
        savePixAsInitiatedInCheckout(donorName);
        
        // Copiar código usando função original
        if (typeof originalCopyPixCode === 'function') {
            originalCopyPixCode();
        } else {
            // Fallback se não existir
            const pixCodeInput = document.getElementById('pixCodeDisplay');
            pixCodeInput.select();
            pixCodeInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            showNotification('Código PIX copiado com sucesso!', 'success');
        }
    }
    
    // Sobrescrever copyPixKey para também salvar como iniciado
    const originalCopyPixKey = window.copyPixKey;
    window.copyPixKey = function() {
        const donorName = document.getElementById('donorName').value.trim();
        
        if (!donorName) {
            showNotification('Por favor, informe seu nome no Passo 1 antes de copiar a chave PIX', 'warning');
            const nameField = document.getElementById('donorName');
            nameField.focus();
            nameField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            nameField.classList.add('is-invalid');
            setTimeout(() => {
                nameField.classList.remove('is-invalid');
            }, 3000);
            return;
        }
        
        // Usar função original (que já salva como iniciado)
        if (typeof originalCopyPixKey === 'function') {
            originalCopyPixKey();
        }
    }
});
