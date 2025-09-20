    </div> <!-- Fim do container principal -->

    <!-- Toast Container para Notificações -->
    <div class="admin-toast-container" id="toastContainer"></div>

    <!-- JavaScript Admin -->
    <script>
        // Configuração global do projeto
        window.BASE_URL = '<?php echo BASE_URL; ?>';

        // Função para mostrar notificações toast
        function showAdminNotification(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');
            
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

        function getIconForType(type) {
            const icons = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        function getColorForType(type) {
            const colors = {
                'success': 'success',
                'error': 'danger',
                'warning': 'warning',
                'info': 'info'
            };
            return colors[type] || 'info';
        }

        function getTitleForType(type) {
            const titles = {
                'success': 'Sucesso',
                'error': 'Erro',
                'warning': 'Atenção',
                'info': 'Informação'
            };
            return titles[type] || 'Informação';
        }

        // Função para atualizar status via AJAX
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
    </script>
</body>
</html>
