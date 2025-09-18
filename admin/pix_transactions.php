<?php

/**
 * Admin - Histórico de Transações PIX
 * Página para visualizar e gerenciar transações PIX
 */

try {
    require_once '../php/config.php';
    require_once '../helpers/functions.php';
    require_once '../functions/pix.php';
    require_once '../functions/gifts_db.php';
} catch (Exception $e) {
    die("Erro ao carregar arquivos: " . $e->getMessage());
}

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . base_url('login'));
    exit;
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $transactionId = sanitizeInput($_POST['transaction_id']);
        $newStatus = sanitizeInput($_POST['status']);

        if (updatePixTransactionStatus($transactionId, $newStatus)) {
            $_SESSION['success_message'] = 'Status da transação atualizado com sucesso!';
        } else {
            $_SESSION['error_message'] = 'Erro ao atualizar status da transação.';
        }

        header('Location: ' . base_url('admin/pix_transactions'));
        exit;
    }
}

// Buscar transações PIX
try {
    $db = Database::getInstance();

    // Query para buscar transações com dados do presente
    $sql = "SELECT pt.*, g.titulo as gift_title, g.valor as gift_value 
            FROM pix_transactions pt 
            LEFT JOIN gifts g ON pt.gift_id = g.id 
            ORDER BY pt.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estatísticas
    $statsSql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN status = 'confirmed' THEN amount ELSE 0 END) as total_confirmed
                 FROM pix_transactions";

    $statsStmt = $db->prepare($statsSql);
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $transactions = [];
    $stats = [
        'total' => 0,
        'pending' => 0,
        'paid' => 0,
        'confirmed' => 0,
        'cancelled' => 0,
        'total_confirmed' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico PIX - <?php echo SITE_TITLE; ?></title>

    <!-- Favicon png-->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .transaction-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }

        .transaction-card.pending {
            border-left-color: #ffc107;
        }

        .transaction-card.paid {
            border-left-color: #28a745;
        }

        .transaction-card.confirmed {
            border-left-color: #17a2b8;
        }

        .transaction-card.cancelled {
            border-left-color: #dc3545;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .amount-display {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo base_url('admin'); ?>">
                <i class="fas fa-gift me-2"></i>
                Admin - Histórico PIX
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
                        <a class="dropdown-item text-white" href="<?php echo base_url('admin'); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
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
                            <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </a></li>
                        </ul>
                    </div>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Mensagens -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $stats['total']; ?></div>
                    <div>Total</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);">
                    <div class="stats-number"><?php echo $stats['pending']; ?></div>
                    <div>Pendentes</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="stats-number"><?php echo $stats['paid']; ?></div>
                    <div>Pagas</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                    <div class="stats-number"><?php echo $stats['confirmed']; ?></div>
                    <div>Confirmadas</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                    <div class="stats-number"><?php echo $stats['cancelled']; ?></div>
                    <div>Canceladas</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                    <div class="stats-number">R$ <?php echo number_format($stats['total_confirmed'], 2, ',', '.'); ?></div>
                    <div>Total Confirmado</div>
                </div>
            </div>
        </div>

        <!-- Lista de Transações -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Histórico de Transações PIX
                    <span class="badge bg-light text-dark ms-2"><?php echo count($transactions); ?> transações</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($transactions)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma transação PIX encontrada</h5>
                        <p class="text-muted">As transações aparecerão aqui quando os convidados começarem a presentear.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($transactions as $transaction): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card transaction-card <?php echo $transaction['status']; ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">
                                                <?php echo htmlspecialchars($transaction['gift_title'] ?? 'Presente não encontrado'); ?>
                                            </h6>
                                            <span class="badge status-badge bg-<?php
                                                                                echo $transaction['status'] === 'pending' ? 'warning' : ($transaction['status'] === 'paid' ? 'success' : ($transaction['status'] === 'confirmed' ? 'info' : 'danger'));
                                                                                ?>">
                                                <?php
                                                echo $transaction['status'] === 'pending' ? 'Pendente' : ($transaction['status'] === 'paid' ? 'Pago' : ($transaction['status'] === 'confirmed' ? 'Confirmado' : 'Cancelado'));
                                                ?>
                                            </span>
                                        </div>

                                        <div class="amount-display mb-2">
                                            R$ <?php echo number_format($transaction['amount'], 2, ',', '.'); ?>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars($transaction['donor_name'] ?: 'Anônimo'); ?>
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?>
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-fingerprint me-1"></i>
                                                ID: <?php echo substr($transaction['transaction_id'], -8); ?>
                                            </small>
                                        </div>

                                        <?php if ($transaction['donor_phone']): ?>
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    <?php echo htmlspecialchars($transaction['donor_phone']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Botões de Ação -->
                                        <div class="d-flex gap-2 mt-3">
                                            <?php if ($transaction['status'] === 'pending'): ?>
                                                <button class="btn btn-success btn-sm" onclick="updateStatus('<?php echo $transaction['transaction_id']; ?>', 'paid')">
                                                    <i class="fas fa-check me-1"></i>Marcar como Pago
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="updateStatus('<?php echo $transaction['transaction_id']; ?>', 'cancelled')">
                                                    <i class="fas fa-times me-1"></i>Cancelar
                                                </button>
                                            <?php elseif ($transaction['status'] === 'paid'): ?>
                                                <button class="btn btn-info btn-sm" onclick="updateStatus('<?php echo $transaction['transaction_id']; ?>', 'confirmed')">
                                                    <i class="fas fa-check-double me-1"></i>Confirmar
                                                </button>
                                                <button class="btn btn-warning btn-sm" onclick="updateStatus('<?php echo $transaction['transaction_id']; ?>', 'pending')">
                                                    <i class="fas fa-undo me-1"></i>Voltar para Pendente
                                                </button>
                                            <?php elseif ($transaction['status'] === 'confirmed'): ?>
                                                <button class="btn btn-warning btn-sm" onclick="updateStatus('<?php echo $transaction['transaction_id']; ?>', 'paid')">
                                                    <i class="fas fa-undo me-1"></i>Voltar para Pago
                                                </button>
                                            <?php endif; ?>
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
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Alteração</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja alterar o status desta transação?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentTransactionId = '';
        let currentStatus = '';

        function updateStatus(transactionId, status) {
            currentTransactionId = transactionId;
            currentStatus = status;

            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        document.getElementById('confirmButton').addEventListener('click', function() {
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
        });
    </script>
</body>

</html>