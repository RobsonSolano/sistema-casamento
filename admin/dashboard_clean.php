<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../php/config.php';
    require_once '../helpers/functions.php';
    require_once '../functions/gifts_db.php';
    require_once '../functions/pix.php';
} catch (Exception $e) {
    die("Erro ao carregar arquivos: " . $e->getMessage());
} catch (Error $e) {
    die("Erro fatal ao carregar arquivos: " . $e->getMessage());
}

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . base_url('login'));
    exit;
}

// Verificar timeout da sessão (1 hora)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: ' . base_url('login'));
    exit;
}

// Definir título da página
$pageTitle = 'Dashboard';

// Processar ações
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'logout':
            session_destroy();
            header('Location: ' . base_url('login'));
            exit;
            break;
            
        case 'add_gift':
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $valor = floatval($_POST['valor'] ?? 0);
            
            if ($titulo && $valor > 0) {
                if (addGift($titulo, $valor)) {
                    $message = 'Presente adicionado com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao adicionar presente.';
                    $messageType = 'danger';
                }
            } else {
                $message = 'Por favor, preencha todos os campos corretamente.';
                $messageType = 'warning';
            }
            break;
            
        case 'update_gift':
            $id = intval($_POST['gift_id'] ?? 0);
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $valor = floatval($_POST['valor'] ?? 0);
            
            if ($id && $titulo && $valor > 0) {
                if (updateGift($id, $titulo, $valor)) {
                    $message = 'Presente atualizado com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao atualizar presente.';
                    $messageType = 'danger';
                }
            } else {
                $message = 'Por favor, preencha todos os campos corretamente.';
                $messageType = 'warning';
            }
            break;
            
        case 'delete_gift':
            $id = intval($_POST['gift_id'] ?? 0);
            
            if ($id) {
                if (deleteGift($id)) {
                    $message = 'Presente removido com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao remover presente.';
                    $messageType = 'danger';
                }
            }
            break;
            
        case 'toggle_gift_status':
            $id = intval($_POST['gift_id'] ?? 0);
            
            if ($id) {
                if (toggleGiftStatus($id)) {
                    $message = 'Status do presente atualizado!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao atualizar status.';
                    $messageType = 'danger';
                }
            }
            break;
    }
}

// Buscar presentes
$search = sanitizeInput($_GET['search'] ?? '');
$statusFilter = sanitizeInput($_GET['status'] ?? '');

$gifts = searchGifts($search, $statusFilter);
$stats = calculateGiftStats();

// Adicionar estatísticas PIX
$stats['total_pix_transactions'] = countPixTransactionsByStatus();
$stats['confirmed_pix_transactions'] = countPixTransactionsByStatus('confirmado');
$stats['pre_confirmed_pix_transactions'] = countPixTransactionsByStatus('pre_confirmado');
$stats['initiated_pix_transactions'] = countPixTransactionsByStatus('iniciado');
$stats['total_pix_amount'] = getTotalConfirmedPixAmount();
?>

<?php include 'header.php'; ?>

        <!-- Toast Notification -->
        <?php if ($message): ?>
            <div class="admin-toast-container">
                <div class="toast admin-toast show" role="alert" id="notificationToast">
                    <div class="toast-header admin-toast-header">
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
                    <div class="toast-body admin-toast-body">
                        <?php echo $message; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-gift fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['total_gifts']; ?></div>
                        <div>Total de Presentes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['available_gifts']; ?></div>
                        <div>Disponíveis</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['purchased_gifts']; ?></div>
                        <div>Comprados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <div class="admin-stats-number">R$ <?php echo number_format($stats['total_value'], 2, ',', '.'); ?></div>
                        <div>Valor Total</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas PIX -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['total_pix_transactions']; ?></div>
                        <div>Transações PIX</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['confirmed_pix_transactions']; ?></div>
                        <div>PIX Confirmados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['pre_confirmed_pix_transactions']; ?></div>
                        <div>PIX Pré Confirmados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <div class="admin-stats-number">R$ <?php echo number_format($stats['total_pix_amount'] ?? 0, 2, ',', '.'); ?></div>
                        <div>Total PIX Confirmado</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Recados -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['total_recados']; ?></div>
                        <div>Total de Recados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="card admin-card mb-4">
            <div class="card-header admin-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtros e Busca
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Buscar Presentes</label>
                        <input type="text" class="form-control admin-form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" placeholder="Digite o nome do presente...">
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select admin-form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Disponível</option>
                            <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Comprado</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary admin-btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Adicionar Presente -->
        <div class="card admin-card mb-4">
            <div class="card-header admin-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Adicionar Novo Presente
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="add_gift">
                    <div class="col-md-6">
                        <label for="titulo" class="form-label">Nome do Presente</label>
                        <input type="text" class="form-control admin-form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="col-md-4">
                        <label for="valor" class="form-label">Valor (R$)</label>
                        <input type="number" class="form-control admin-form-control" id="valor" name="valor" 
                               step="0.01" min="0" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary admin-btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Presentes -->
        <div class="card admin-card">
            <div class="card-header admin-card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Presentes
                    <span class="badge admin-badge bg-light text-dark ms-2"><?php echo count($gifts); ?> presentes</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($gifts)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum presente encontrado</h5>
                        <p class="text-muted">Adicione alguns presentes para começar!</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gifts as $gift): ?>
                                    <tr>
                                        <td><?php echo $gift['id']; ?></td>
                                        <td><?php echo htmlspecialchars($gift['titulo']); ?></td>
                                        <td>R$ <?php echo number_format($gift['valor'], 2, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge admin-badge bg-<?php echo $gift['status'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $gift['status'] ? 'Comprado' : 'Disponível'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($gift['data_criacao'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary admin-btn-sm" 
                                                        data-bs-toggle="modal" data-bs-target="#editModal<?php echo $gift['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="toggle_gift_status">
                                                    <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-<?php echo $gift['status'] ? 'warning' : 'success'; ?> admin-btn-sm">
                                                        <i class="fas fa-<?php echo $gift['status'] ? 'undo' : 'check'; ?>"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este presente?');">
                                                    <input type="hidden" name="action" value="delete_gift">
                                                    <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger admin-btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal de Edição -->
                                    <div class="modal fade" id="editModal<?php echo $gift['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Presente</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="update_gift">
                                                        <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="edit_titulo_<?php echo $gift['id']; ?>" class="form-label">Nome do Presente</label>
                                                            <input type="text" class="form-control admin-form-control" 
                                                                   id="edit_titulo_<?php echo $gift['id']; ?>" name="titulo" 
                                                                   value="<?php echo htmlspecialchars($gift['titulo']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_valor_<?php echo $gift['id']; ?>" class="form-label">Valor (R$)</label>
                                                            <input type="number" class="form-control admin-form-control" 
                                                                   id="edit_valor_<?php echo $gift['id']; ?>" name="valor" 
                                                                   value="<?php echo $gift['valor']; ?>" step="0.01" min="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary admin-btn-primary">Salvar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

<?php include 'footer.php'; ?>

