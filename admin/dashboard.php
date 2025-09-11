<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../php/config.php';
    require_once '../helpers/functions.php';
    require_once '../functions/gifts_db.php';
} catch (Exception $e) {
    die("Erro ao carregar arquivos: " . $e->getMessage());
} catch (Error $e) {
    die("Erro fatal ao carregar arquivos: " . $e->getMessage());
}

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Verificar timeout da sessão (1 hora)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: login.php');
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
            header('Location: login.php');
            exit;
    }
}

// Obter dados
$search = sanitizeInput($_GET['search'] ?? '');
$statusFilter = sanitizeInput($_GET['status'] ?? '');

$gifts = searchGifts($search, $statusFilter);
$stats = calculateGiftStats();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gift me-2"></i>
                Admin - Lista de Presentes
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        <?php echo $_SESSION['admin_email']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Ver Site
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
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
            </div>
        </div>
    </nav>
    
    <div class="container-fluid py-4">
        <!-- Mensagens -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <div class="stats-number"><?php echo $stats['available_gifts']; ?></div>
                        <div>Disponíveis</div>
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
        </div>
        
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Título do presente...">
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Disponíveis</option>
                            <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Comprados</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <?php if (!empty($search) || !empty($statusFilter)): ?>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
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
                                <th>Status</th>
                                <th>Data Criação</th>
                                <th style="width: 280px;">Ações</th>
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
                                        <td>
                                            <strong><?php echo htmlspecialchars($gift['titulo']); ?></strong>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?php echo formatCurrency($gift['valor']); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($gift['status'] == 1): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Comprado
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Disponível
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDate($gift['data_criacao'], 'd/m/Y H:i'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <?php if ($gift['status'] == 1): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-warning me-3">
                                                            <i class="fas fa-undo me-1"></i>Marcar Disponível
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-success me-3">
                                                            <i class="fas fa-check me-1"></i>Marcar Comprado
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
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
                    '0': {pattern: /[0-9]/}
                }
            });
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        });
        
        // Função para confirmar exclusão
        function confirmDelete(giftId) {
            $('#deleteGiftId').val(giftId);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>
