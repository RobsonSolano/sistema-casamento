/**
 * JavaScript centralizado para área administrativa
 * Funcionalidades compartilhadas entre todas as páginas admin
 */

// Configuração global do projeto
window.BASE_URL = window.BASE_URL || '';

// ============================================================================
// FUNÇÕES DE NOTIFICAÇÃO TOAST
// ============================================================================

/**
 * Mostra notificação toast
 * @param {string} message - Mensagem a ser exibida
 * @param {string} type - Tipo da notificação (success, error, warning, info)
 */
function showAdminNotification(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    
    if (!toastContainer) {
        console.warn('Toast container não encontrado');
        return;
    }
    
    const toastHtml = `
        <div class="toast admin-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header admin-toast-header">
                <i class="fas fa-${getIconForType(type)} me-2 text-${getColorForType(type)}"></i>
                <strong class="me-auto">${getTitleForType(type)}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body admin-toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 4000
    });
    
    toast.show();
    
    // Remove o elemento após ser escondido
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

/**
 * Retorna ícone baseado no tipo de notificação
 * @param {string} type - Tipo da notificação
 * @returns {string} Nome do ícone Font Awesome
 */
function getIconForType(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Retorna cor baseada no tipo de notificação
 * @param {string} type - Tipo da notificação
 * @returns {string} Classe CSS de cor
 */
function getColorForType(type) {
    const colors = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
    };
    return colors[type] || 'info';
}

/**
 * Retorna título baseado no tipo de notificação
 * @param {string} type - Tipo da notificação
 * @returns {string} Título da notificação
 */
function getTitleForType(type) {
    const titles = {
        'success': 'Sucesso',
        'error': 'Erro',
        'warning': 'Atenção',
        'info': 'Informação'
    };
    return titles[type] || 'Informação';
}

// ============================================================================
// FUNÇÕES PARA TRANSAÇÕES PIX
// ============================================================================

let currentTransactionId = '';
let currentStatus = '';

/**
 * Atualiza status de transação PIX via AJAX
 * @param {string} transactionId - ID da transação
 * @param {string} newStatus - Novo status
 */
function updateStatus(transactionId, newStatus) {
    if (!confirm('Tem certeza que deseja alterar o status desta transação?')) {
        return;
    }

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
            showAdminNotification('Status atualizado com sucesso!', 'success');
            // Recarregar a página após 1 segundo
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAdminNotification('Erro ao atualizar status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showAdminNotification('Erro ao atualizar status: ' + error.message, 'error');
    });
}

/**
 * Prepara dados para atualização de status (versão com modal)
 * @param {string} transactionId - ID da transação
 * @param {string} status - Status a ser aplicado
 */
function prepareStatusUpdate(transactionId, status) {
    currentTransactionId = transactionId;
    currentStatus = status;

    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

/**
 * Confirma e executa atualização de status via formulário
 */
function confirmStatusUpdate() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'update_status';

    const transactionInput = document.createElement('input');
    transactionInput.type = 'hidden';
    transactionInput.name = 'transaction_id';
    transactionInput.value = currentTransactionId;

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = currentStatus;

    form.appendChild(actionInput);
    form.appendChild(transactionInput);
    form.appendChild(statusInput);

    document.body.appendChild(form);
    form.submit();
}

// ============================================================================
// FUNÇÕES PARA RECADOS
// ============================================================================

/**
 * Confirma exclusão de recado
 * @param {number} recadoId - ID do recado
 */
function confirmDelete(recadoId) {
    document.getElementById('deleteRecadoId').value = recadoId;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

/**
 * Alterna entre expandir/contrair mensagem de recado
 * @param {number} recadoId - ID do recado
 */
function toggleMessage(recadoId) {
    const messageDiv = document.getElementById('message-' + recadoId);
    const expandBtn = messageDiv.nextElementSibling.querySelector('.expand-btn');
    const btnText = expandBtn.querySelector('.btn-text');

    if (messageDiv.classList.contains('collapsed')) {
        // Expandir
        messageDiv.classList.remove('collapsed');
        btnText.innerHTML = '-<span class="btn-text-text">Minimizar</span>';
        expandBtn.title = 'Minimizar mensagem';
    } else {
        // Contrair
        messageDiv.classList.add('collapsed');
        btnText.innerHTML = '+<span class="btn-text-text">Expandir</span>';
        expandBtn.title = 'Expandir mensagem';
    }
}

// ============================================================================
// FUNÇÕES DE INICIALIZAÇÃO
// ============================================================================

/**
 * Inicializa funcionalidades comuns da área admin
 */
function initAdmin() {
    // Auto-hide toast notifications
    const toast = document.getElementById('notificationToast');
    if (toast) {
        setTimeout(function() {
            toast.classList.add('toast-slide-out');
            setTimeout(function() {
                toast.style.transition = 'opacity 0.3s ease';
                toast.style.opacity = '0';
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 300);
        }, 2000);
    }

    // Configurar botão de confirmação de status (se existir)
    const confirmButton = document.getElementById('confirmButton');
    if (confirmButton) {
        confirmButton.addEventListener('click', confirmStatusUpdate);
    }
}

// ============================================================================
// INICIALIZAÇÃO AUTOMÁTICA
// ============================================================================

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initAdmin);

// Compatibilidade com jQuery (se disponível)
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        initAdmin();
    });
}

// ============================================================================
// UTILITÁRIOS
// ============================================================================

/**
 * Formata valor monetário
 * @param {number} value - Valor a ser formatado
 * @returns {string} Valor formatado
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

/**
 * Formata data
 * @param {string|Date} date - Data a ser formatada
 * @param {string} format - Formato desejado
 * @returns {string} Data formatada
 */
function formatDate(date, format = 'dd/mm/yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year)
        .replace('HH', hours)
        .replace('ii', minutes);
}

/**
 * Sanitiza entrada do usuário
 * @param {string} input - Entrada a ser sanitizada
 * @returns {string} Entrada sanitizada
 */
function sanitizeInput(input) {
    if (typeof input !== 'string') return '';
    return input.trim().replace(/[<>]/g, '');
}

// ============================================================================
// EXPORTS (para uso em módulos)
// ============================================================================

// Disponibilizar funções globalmente para compatibilidade
window.showAdminNotification = showAdminNotification;
window.updateStatus = updateStatus;
window.prepareStatusUpdate = prepareStatusUpdate;
window.confirmDelete = confirmDelete;
window.toggleMessage = toggleMessage;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.sanitizeInput = sanitizeInput;
