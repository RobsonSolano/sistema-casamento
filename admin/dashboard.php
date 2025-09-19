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

// Processar ações
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_gift':
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $valor = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor'] ?? '0'));

            $errors = validateGiftData($titulo, $valor);

            if (empty($errors)) {
                if (addGift($titulo, $valor)) {
                    $message = 'Presente adicionado com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao adicionar presente';
                    $messageType = 'danger';
                }
            } else {
                $message = implode(', ', $errors);
                $messageType = 'danger';
            }
            break;

        case 'toggle_status':
            $giftId = intval($_POST['gift_id'] ?? 0);
            if ($giftId > 0 && toggleGiftStatus($giftId)) {
                $message = 'Status atualizado com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao atualizar status';
                $messageType = 'danger';
            }
            break;

        case 'edit_gift':
            $giftId = intval($_POST['gift_id'] ?? 0);
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $valor = floatval(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['valor'] ?? '0'));

            $errors = validateGiftData($titulo, $valor);

            if (empty($errors)) {
                if (updateGift($giftId, $titulo, $valor, $_POST['status'] ?? 0)) {
                    $message = 'Presente atualizado com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao atualizar presente';
                    $messageType = 'danger';
                }
            } else {
                $message = implode(', ', $errors);
                $messageType = 'danger';
            }
            break;

        case 'delete_gift':
            $giftId = intval($_POST['gift_id'] ?? 0);
            if ($giftId > 0 && deleteGift($giftId)) {
                $message = 'Presente removido com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao remover presente';
                $messageType = 'danger';
            }
            break;

        case 'logout':
            session_destroy();
            header('Location: ' . base_url('login'));
            exit;
    }
}

// Obter dados
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
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>

    <!-- Favicon png-->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            border-radius: 50px;
        }

        .btn-sm {
            border-radius: 20px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .badge {
            border-radius: 20px;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        .toast {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            min-width: 350px;
            animation: toastSlideIn 0.3s ease-out;
        }

        .toast-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 20px 20px 0 0;
            padding: 1rem 1.5rem 0.5rem;
        }

        .toast-body {
            padding: 1rem 1.5rem 1.5rem;
            font-weight: 500;
        }

        .toast-success .toast-header {
            color: #198754;
        }

        .toast-danger .toast-header {
            color: #dc3545;
        }

        .toast-warning .toast-header {
            color: #fd7e14;
        }

        .toast-info .toast-header {
            color: #0dcaf0;
        }

        @keyframes toastSlideIn {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0);
            }

            99% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }

            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @keyframes toastSlideOut {
            0% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            1% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0);
            }

            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0);
            }
        }

        .toast-slide-out {
            animation: toastSlideOut 0.5s ease-in forwards;
        }
    </style>
</head>

<body class="pb-5">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gift me-2"></i>
                Admin - Lista de Presentes
            </a>

            <div class="navbar-nav ms-auto d-flex align-items-center gap-2 justify-content-end">
                <ul class="navbar-nav d-flex align-items-center gap-2">
                    <li>
                        <a class="dropdown-item text-white" href="<?php echo base_url('presentes'); ?>" target="_blank">
                            <i class="fas fa-gift me-2"></i>Lista de Presentes
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-white" href="<?php echo base_url('admin/recados'); ?>">
                            <i class="fas fa-comments me-2"></i>Recados
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-white" href="<?php echo base_url('admin/pix_transactions'); ?>">
                            <i class="fas fa-credit-card me-2"></i>Histórico PIX
                        </a>
                    </li>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i>
                            <?php echo $_SESSION['admin_email']; ?>
                        </a>
                        <ul class="dropdown-menu">

                            <li><a class="dropdown-item" href="<?php echo base_url(); ?>" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Ver Site
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="logout">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
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
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-gift fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['total_gifts']; ?></div>
                        <div>Total de Presentes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['purchased_gifts']; ?></div>
                        <div>Comprados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['purchase_percentage']; ?>%</div>
                        <div>Comprados</div>
                    </div>
                </div>
            </div>
            <!-- Total de recados -->
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['total_recados']; ?></div>
                        <div>Total de Recados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda linha de estatísticas -->
        <div class="row mb-4">
            <!-- Total PIX -->
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-2x mb-2 text-primary"></i>
                        <div class="stats-number"><?php echo $stats['total_pix_transactions']; ?></div>
                        <div>Transações PIX</div>
                    </div>
                </div>
            </div>
            <!-- PIX Confirmados -->
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <div class="stats-number"><?php echo $stats['confirmed_pix_transactions']; ?></div>
                        <div>PIX Confirmados</div>
                    </div>
                </div>
            </div>
            <!-- PIX Pré Confirmados -->
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2 text-warning"></i>
                        <div class="stats-number"><?php echo $stats['pre_confirmed_pix_transactions']; ?></div>
                        <div>PIX Pré Confirmados</div>
                    </div>
                </div>
            </div>
            <!-- Valor Total PIX -->
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2 text-success"></i>
                        <div class="stats-number">R$ <?php echo number_format($stats['total_pix_amount'] ?? 0, 2, ',', '.'); ?></div>
                        <div>Total PIX Confirmado</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-9">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Título do presente...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <?php if (!empty($search) || !empty($statusFilter)): ?>
                                <a href="<?php echo base_url('admin'); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Limpar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cadastrar Presente -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Cadastrar Novo Presente</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="add_gift">

                    <div class="col-md-6">
                        <label for="titulo" class="form-label">Título do Presente *</label>
                        <input type="text"
                            class="form-control"
                            id="titulo"
                            name="titulo"
                            placeholder="Ex: Jogo de Pratos"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="valor" class="form-label">Valor *</label>
                        <input type="text"
                            class="form-control"
                            id="valor"
                            name="valor"
                            placeholder="R$ 0,00"
                            required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Presente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Presentes -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Presentes
                    <span class="badge bg-light text-dark ms-2"><?php echo count($gifts); ?> itens</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Valor</th>
                                <th>Data Criação</th>
                                <th style="width: 120px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($gifts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                                        <br>
                                        <span class="text-muted">Nenhum presente encontrado</span>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gifts as $gift): ?>
                                    <tr>
                                        <td class="d-flex align-items-center gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary me-2"
                                                onclick="editGift(<?php echo $gift['id']; ?>, '<?php echo htmlspecialchars($gift['titulo'], ENT_QUOTES); ?>', '<?php echo $gift['valor']; ?>', <?php echo $gift['status']; ?>)"
                                                title="Editar">
                                                <i class="fas fa-edit me-1"></i>
                                            </button>
                                            <span><strong><?php echo htmlspecialchars($gift['titulo']); ?></strong></span>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?php echo formatCurrency($gift['valor']); ?></strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDate($gift['data_criacao'], 'd/m/Y H:i'); ?>
                                            </small>
                                        </td>
                                        <td style="width: 120px;">
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete(<?php echo $gift['id']; ?>)"
                                                    title="Remover">
                                                    <i class="fas fa-trash me-1"></i>Deletar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Presente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    <input type="hidden" name="action" value="edit_gift">
                    <input type="hidden" name="gift_id" id="editGiftId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editTitulo" class="form-label">Título do Presente *</label>
                            <input type="text"
                                class="form-control"
                                id="editTitulo"
                                name="titulo"
                                placeholder="Ex: Jogo de Pratos"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="editValor" class="form-label">Valor *</label>
                            <input type="text"
                                class="form-control"
                                id="editValor"
                                name="valor"
                                placeholder="R$ 0,00"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="0">Disponível</option>
                                <option value="1">Comprado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
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
                    <p>Tem certeza que deseja remover este presente?</p>
                    <p class="text-muted">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <form method="POST" id="deleteForm" class="d-inline">
                        <input type="hidden" name="action" value="delete_gift">
                        <input type="hidden" name="gift_id" id="deleteGiftId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Sim, Deletar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(document).ready(function() {
            // Máscara para valor em real
            $('#valor').mask('R$ 000.000.000,00', {
                reverse: true,
                translation: {
                    '0': {
                        pattern: /[0-9]/
                    }
                }
            });

            // Máscara para valor em real no modal de edição
            $('#editValor').mask('R$ 000.000.000,00', {
                reverse: true,
                translation: {
                    '0': {
                        pattern: /[0-9]/
                    }
                }
            });

            // Auto-hide toast notification
            const toast = $('#notificationToast');
            if (toast.length) {
                setTimeout(function() {
                    toast.addClass('toast-slide-out');
                    setTimeout(function() {
                        toast.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 300);
                }, 2000);
            }
        });

        // Função para editar presente
        function editGift(id, titulo, valor, status) {
            $('#editGiftId').val(id);
            $('#editTitulo').val(titulo);
            $('#editValor').val('R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#editStatus').val(status);
            $('#editModal').modal('show');
        }

        // Função para confirmar exclusão
        function confirmDelete(giftId) {
            $('#deleteGiftId').val(giftId);
            $('#deleteModal').modal('show');
        }

        // Função para mostrar toast personalizado
        function showToast(message, type = 'info') {
            const toastHtml = `
                <div class="toast-container">
                    <div class="toast toast-${type} show" role="alert" id="customToast">
                        <div class="toast-header">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-triangle' : (type === 'warning' ? 'exclamation-triangle' : 'info-circle'))} me-2"></i>
                            <strong class="me-auto">
                                ${type === 'success' ? 'Sucesso!' : (type === 'danger' ? 'Erro!' : (type === 'warning' ? 'Atenção!' : 'Informação'))}
                            </strong>
                        </div>
                        <div class="toast-body">
                            ${message}
                        </div>
                    </div>
                </div>
            `;

            // Remove toast anterior se existir
            $('.toast-container').remove();

            // Adiciona novo toast
            $('body').append(toastHtml);

            // Auto-hide após 2 segundos
            setTimeout(function() {
                $('#customToast').addClass('toast-slide-out');
                setTimeout(function() {
                    $('#customToast').fadeOut(300, function() {
                        $(this).closest('.toast-container').remove();
                    });
                }, 300);
            }, 2000);
        }
    </script>
</body>

</html>