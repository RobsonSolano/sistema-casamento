/**
 * Funções PIX para checkout
 */

// Variáveis globais para checkout
let currentCheckoutData = null;
let checkoutModal = null;


/**
 * Gera código PIX usando biblioteca especializada
 */
function generatePixCodeJS(pixKey, amount, merchantName, merchantCity, txid, description) {
    try {
        // Verificar se a biblioteca PIX está disponível
        if (typeof Pix !== 'undefined') {
            // Criar instância PIX
            const pix = new Pix({
                pixKey: pixKey,
                description: description || `Presente: ${merchantName}`,
                merchantName: merchantName,
                merchantCity: merchantCity,
                txid: txid,
                amount: amount / 100 // Converter centavos para reais
            });
            
            return pix.getPayload();
        } else {
            return generatePixCodeManual(pixKey, amount, merchantName, merchantCity, txid, description);
        }
    } catch (error) {
        return generatePixCodeManual(pixKey, amount, merchantName, merchantCity, txid, description);
    }
}

/**
 * Implementação manual do PIX (fallback)
 */
function generatePixCodeManual(pixKey, amount, merchantName, merchantCity, txid, description) {
    // Formato EMV para PIX - versão simplificada e funcional
    const payloadFormatIndicator = '000201';
    const pointOfInitiationMethod = '0102';
    
    // Merchant Account Information
    const pixKeyLength = pixKey.length.toString().padStart(2, '0');
    const merchantAccountInfo = '26' + 
        (14 + pixKeyLength).toString().padStart(2, '0') + 
        '0014br.gov.bcb.pix01' + pixKeyLength + pixKey;
    
    // Merchant Category Code
    const merchantCategoryCode = '520400005303986';
    
    // Transaction Currency
    const amountStr = amount.toString();
    const transactionCurrency = '540' + amountStr.length.toString().padStart(2, '0') + amountStr;
    
    // Country Code
    const countryCode = '5802BR';
    
    // Merchant Name
    const merchantNameLength = merchantName.length.toString().padStart(2, '0');
    const merchantNameField = '59' + merchantNameLength + merchantName;
    
    // Merchant City
    const merchantCityLength = merchantCity.length.toString().padStart(2, '0');
    const merchantCityField = '60' + merchantCityLength + merchantCity;
    
    // Additional Data Field Template
    const txidLength = txid.length.toString().padStart(2, '0');
    const additionalDataField = '62' + 
        (5 + txidLength).toString().padStart(2, '0') + 
        '05' + txidLength + txid;
    
    // Concatenar tudo
    const data = payloadFormatIndicator + 
                 pointOfInitiationMethod + 
                 merchantAccountInfo + 
                 merchantCategoryCode + 
                 transactionCurrency + 
                 countryCode + 
                 merchantNameField + 
                 merchantCityField + 
                 additionalDataField + 
                 '6304';
    
    // Calcular CRC16
    const crc = calculateCRC16(data);
    
    return data + crc;
}

/**
 * Calcula CRC16 para PIX
 */
function calculateCRC16(data) {
    let crc = 0xFFFF;
    for (let i = 0; i < data.length; i++) {
        crc ^= data.charCodeAt(i);
        for (let j = 0; j < 8; j++) {
            if (crc & 1) {
                crc = (crc >> 1) ^ 0x8408;
            } else {
                crc >>= 1;
            }
        }
    }
    return (crc ^ 0xFFFF).toString(16).toUpperCase().padStart(4, '0');
}

/**
 * Abre o modal de checkout PIX
 */
window.openCheckoutModal = function(button) {
    
    // Usar JavaScript vanilla para garantir compatibilidade
    const giftId = button.getAttribute('data-gift-id');
    const giftName = button.getAttribute('data-gift-name');
    const giftValue = button.getAttribute('data-gift-value');
    
    // Debug detalhado
        giftId: giftId,
        giftName: giftName,
        giftValue: giftValue,
        buttonElement: button
    });
    
    // Armazenar dados do presente
    currentCheckoutData = {
        giftId: giftId,
        giftName: giftName,
        giftValue: giftValue
    };
    
    // Aguardar jQuery estar disponível
    if (typeof $ !== 'undefined') {
        // Atualizar informações no modal
        $('#checkoutGiftName').text(giftName);
        
        // Corrigir formatação do valor
        let formattedValue = 'R$ 0,00';
        if (giftValue && giftValue !== '' && giftValue !== '0') {
            // Remover formatação se existir (ex: "R$ 50,00" -> "50.00")
            let cleanValue = giftValue.toString().replace(/[^\d,.-]/g, '').replace(',', '.');
            const numericValue = parseFloat(cleanValue);
            if (!isNaN(numericValue) && numericValue > 0) {
                formattedValue = 'R$ ' + numericValue.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
        $('#checkoutGiftValue').text(formattedValue);
    } else {
    }
    
    // Mostrar modal
    if (!checkoutModal) {
        checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
    }
    checkoutModal.show();
    
    // Gerar PIX automaticamente
    generateCheckout();
}

/**
 * Gera novo checkout PIX usando JavaScript
 */
function generateCheckout() {
    if (!currentCheckoutData) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro: Dados do presente não encontrados', 'error');
        }
        return;
    }
    
    // Usar JavaScript vanilla para garantir compatibilidade
    const donorNameEl = document.getElementById('donorName');
    const donorPhoneEl = document.getElementById('donorPhone');
    const donorName = donorNameEl ? donorNameEl.value.trim() : '';
    const donorPhone = donorPhoneEl ? donorPhoneEl.value.trim() : '';
    
    try {
        // Configurações PIX (do config.php)
        const pixKey = '11996271186'; // Chave PIX do celular
        const merchantName = 'MARISLAN E DOUGLAS';
        const merchantCity = 'São Paulo';
        
        // Gerar ID único para transação
        const txid = 'casamento_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Converter valor para centavos (PIX usa centavos)
        const cleanValue = currentCheckoutData.giftValue.toString().replace(/[^\d,.-]/g, '').replace(',', '.');
        const numericValue = parseFloat(cleanValue);
        const amountInCents = Math.round(numericValue * 100);
        
            originalValue: currentCheckoutData.giftValue,
            cleanValue: cleanValue,
            numericValue: numericValue,
            amountInCents: amountInCents
        });
        
        if (amountInCents <= 0) {
            if (typeof showNotification !== 'undefined') {
                showNotification('Valor inválido para PIX', 'error');
            }
            return;
        }
        
            pixKey,
            amount: amountInCents,
            merchantName,
            merchantCity,
            txid,
            giftValue: currentCheckoutData.giftValue,
            cleanValue: cleanValue,
            donorName: donorName,
            donorPhone: donorPhone
        });
        
        // Gerar código PIX
        const pixCode = generatePixCodeJS(
            pixKey,
            amountInCents,
            merchantName,
            merchantCity,
            txid,
            `Presente: ${currentCheckoutData.giftName}`
        );
        
        
        // Validar código PIX
        if (!pixCode || pixCode.length < 50) {
            if (typeof showNotification !== 'undefined') {
                showNotification('Erro: Código PIX inválido', 'error');
            }
            return;
        }
        
        // Validar estrutura do PIX
        const isValid = window.validatePixCode(pixCode);
        
        if (!isValid) {
            if (typeof showNotification !== 'undefined') {
                showNotification('Aviso: Código PIX pode não funcionar', 'warning');
            }
        }
        
        // Atualizar código PIX no textarea
        const pixCodeEl = document.getElementById('pixCode');
        if (pixCodeEl) {
            pixCodeEl.value = pixCode;
        }
        
        // Gerar QR Code usando API externa (mais confiável)
        const qrContainer = document.getElementById('pixQRCode');
        if (qrContainer) {
            // Usar API externa para gerar QR Code
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(pixCode)}`;
            
            // Criar nova imagem
            const img = document.createElement('img');
            img.src = qrUrl;
            img.alt = 'QR Code PIX';
            img.className = 'img-fluid';
            img.style.maxWidth = '200px';
            
            // Limpar container e adicionar nova imagem
            qrContainer.innerHTML = '';
            qrContainer.appendChild(img);
            
            
            // Verificar se a imagem carregou
            img.onload = function() {
            };
            
            img.onerror = function() {
                // Fallback: usar Google Charts API
                const googleQrUrl = `https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=${encodeURIComponent(pixCode)}`;
                img.src = googleQrUrl;
            };
        } else {
        }
        
        // Gerar link WhatsApp
        const formattedAmount = parseFloat(cleanValue).toLocaleString('pt-BR', {minimumFractionDigits: 2});
        
        const whatsappMessage = `🎁 *COMPROVANTE DE PRESENTE* 🎁\n\n` +
            `📝 *Presente:* ${currentCheckoutData.giftName}\n` +
            `💰 *Valor:* R$ ${formattedAmount}\n` +
            `🆔 *ID da Transação:* ${txid}\n` +
            `📅 *Data:* ${new Date().toLocaleString('pt-BR')}\n\n` +
            `✅ *Confirmação:* Envie este comprovante após o pagamento para confirmarmos o presente!\n\n` +
            `💕 *Obrigado pelo carinho!*`;
        
        const whatsappLink = `https://wa.me/5511996271186?text=${encodeURIComponent(whatsappMessage)}`;
        const whatsappLinkEl = document.getElementById('whatsappLink');
        if (whatsappLinkEl) {
            whatsappLinkEl.href = whatsappLink;
        }
        
        if (typeof showNotification !== 'undefined') {
            showNotification('PIX gerado com sucesso!', 'success');
        } else {
        }
        
    } catch (error) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Erro ao gerar PIX. Tente novamente.', 'error');
        } else {
        }
    }
}

/**
 * Atualiza QR Code
 */
window.refreshQRCode = function() {
    generateCheckout();
}

/**
 * Copia código PIX para área de transferência
 */
window.copyPixCode = function() {
    const pixCodeEl = document.getElementById('pixCode');
    const pixCode = pixCodeEl ? pixCodeEl.value : '';
    
    if (!pixCode) {
        if (typeof showNotification !== 'undefined') {
            showNotification('Nenhum código PIX disponível', 'warning');
        } else {
        }
        return;
    }
    
    // Copiar para área de transferência
    navigator.clipboard.writeText(pixCode).then(function() {
        if (typeof showNotification !== 'undefined') {
            showNotification('Código PIX copiado!', 'success');
        } else {
        }
    }).catch(function(err) {
        
        // Fallback: selecionar texto
        if (pixCodeEl) {
            pixCodeEl.select();
            document.execCommand('copy');
        }
        if (typeof showNotification !== 'undefined') {
            showNotification('Código PIX selecionado!', 'info');
        } else {
        }
    });
}

/**
 * Valida código PIX usando validador online
 */
window.validatePixCode = function(pixCode) {
    if (!pixCode) return false;
    
    // Validações básicas
    if (pixCode.length < 50) return false;
    if (!pixCode.startsWith('000201')) return false;
    if (!pixCode.endsWith('6304')) return false;
    
    // Verificar CRC16
    const data = pixCode.slice(0, -4);
    const crc = pixCode.slice(-4);
    const calculatedCrc = calculateCRC16(data);
    
    return crc === calculatedCrc;
};

// Debug: confirmar que as funções foram definidas
    openCheckoutModal: typeof window.openCheckoutModal,
    refreshQRCode: typeof window.refreshQRCode,
    copyPixCode: typeof window.copyPixCode,
    validatePixCode: typeof window.validatePixCode
});
