<?php

/**
 * Página de Administração - Recados
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../functions/recados_db.php';

// Verificar se está logado
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . base_url('login'));
    exit;
}

// Definir título da página
$pageTitle = 'Recados';

// Processar ações
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'delete_recado':
            $recadoId = intval($_POST['recado_id'] ?? 0);
            if ($recadoId > 0 && deleteRecado($recadoId)) {
                $message = 'Recado removido com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao remover recado';
                $messageType = 'danger';
            }
            break;

        case 'logout':
            session_destroy();
            header('Location: ' . base_url('login'));
            exit;
    }
}

// Buscar recados
$search = $_GET['search'] ?? '';
$recados = !empty($search) ? searchRecados($search) : getAllRecados();
$stats = calculateRecadosStats();
?>

<?php include 'navbar.php'; ?>

</head>

        <!-- Toast Notification -->
        <?php if ($message): ?>
            <div class="toast-container">
                <div class="toast toast-<?php echo $messageType; ?> show" role="alert" id="notificationToast">
                    <div class="toast-header">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'danger' ? 'exclamation-triangle' : ($messageType === 'warning' ? 'exclamation-triangle' : 'info-circle')); ?> me-2"></i>
                        <strong class="me-auto">
                            <?php
                            switch ($messageType) {
                                case 'success':
                                    echo 'Sucesso!';
                                    break;
                                case 'danger':
                                    echo 'Erro!';
                                    break;
                                case 'warning':
                                    echo 'Atenção!';
                                    break;
                                default:
                                    echo 'Informação';
                                    break;
                            }
                            ?>
                        </strong>
                    </div>
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4 cards-recados">
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['total_recados']; ?></div>
                        <div>Total</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body p-1 text-center">
                        <i class="fas fa-calendar-day fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['today_recados']; ?></div>
                        <div>Hoje</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body p-1 text-center">
                        <i class="fas fa-calendar-week fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['week_recados']; ?></div>
                        <div>Esta Semana</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body p-1 text-center">
                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['month_recados']; ?></div>
                        <div>Este Mês</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-search me-2"></i>Filtros
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Nome ou mensagem...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                        <!-- Se existir uma busca, mostrar o botão de limpar -->
                        <?php if (!empty($search)): ?>
                            <a href="<?php echo base_url('admin/recados'); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Limpar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Recados -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>Recados dos Convidados
                    <span class="badge bg-light text-dark ms-2"><?php echo count($recados); ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($recados)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum recado encontrado</h5>
                        <p class="text-muted">
                            <?php if (!empty($search)): ?>
                                Tente ajustar os filtros de busca.
                            <?php else: ?>
                                Os recados dos convidados aparecerão aqui.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($recados as $recado): ?>
                            <div class="col-12 col-lg-6 mb-3 md-lg-2">
                                <div class="card recado-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                <?php echo htmlspecialchars($recado['nome']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo formatDate($recado['data_envio'], 'd/m/Y H:i'); ?>
                                            </small>
                                        </div>

                                        <div class="recado-message collapsed" id="message-<?php echo $recado['id']; ?>">
                                            <?php echo nl2br(htmlspecialchars($recado['mensagem'])); ?>
                                        </div>

                                        <div class="text-center mt-3">
                                            <button type="button"
                                                class="expand-btn"
                                                onclick="toggleMessage(<?php echo $recado['id']; ?>)"
                                                title="Expandir mensagem">
                                                <span class="btn-text d-flex align-items-center gap-2">+<span class="btn-text-text">Expandir</span></span>
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <?php echo formatDate($recado['data_envio'], 'd/m/Y H:i:s'); ?>
                                            </small>

                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete(<?php echo $recado['id']; ?>)"
                                                title="Remover">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja remover este recado?</p>
                    <p class="text-muted">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <form method="POST" id="deleteForm" class="d-inline">
                        <input type="hidden" name="action" value="delete_recado">
                        <input type="hidden" name="recado_id" id="deleteRecadoId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Sim, Deletar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Funções já disponíveis no admin.js
        // confirmDelete() e toggleMessage() são carregadas automaticamente
    </script>

<?php include 'footer.php'; ?>