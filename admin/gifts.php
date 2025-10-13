<?php
// Habilitar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../php/config.php';
    require_once '../helpers/functions.php';
    require_once '../functions/gifts_db.php';
    require_once '../helpers/image_upload.php';
} catch (Exception $e) {
    die("Erro ao carregar arquivos: " . $e->getMessage());
} catch (Error $e) {
    die("Erro fatal ao carregar arquivos: " . $e->getMessage());
}

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . base_url('admin/login'));
    exit;
}

// Verificar timeout da sessão (1 hora)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: ' . base_url('admin/login'));
    exit;
}

// Definir título da página
$pageTitle = 'Gerenciar Presentes';

// Processar ações
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_gift':
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $valor = floatval($_POST['valor'] ?? 0);
            $imagemFilename = null;
            
            // Processar upload de imagem se houver
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = uploadGiftImage($_FILES['imagem']);
                if ($uploadResult['success']) {
                    $imagemFilename = $uploadResult['filename'];
                } else {
                    $message = $uploadResult['error'];
                    $messageType = 'danger';
                    break;
                }
            }
            
            if ($titulo && $valor > 0) {
                if (addGift($titulo, $valor, $imagemFilename)) {
                    $message = 'Presente adicionado com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao adicionar presente.';
                    $messageType = 'danger';
                    // Deletar imagem se falhou ao salvar no banco
                    if ($imagemFilename) {
                        deleteGiftImage($imagemFilename);
                    }
                }
            } else {
                $message = 'Por favor, preencha todos os campos corretamente.';
                $messageType = 'warning';
                // Deletar imagem se validação falhou
                if ($imagemFilename) {
                    deleteGiftImage($imagemFilename);
                }
            }
            break;
            
        case 'update_gift':
            $id = intval($_POST['gift_id'] ?? 0);
            $titulo = sanitizeInput($_POST['titulo'] ?? '');
            $valor = floatval($_POST['valor'] ?? 0);
            $imagemFilename = null;
            
            // Buscar presente atual para pegar imagem antiga
            $currentGift = getGiftById($id);
            $oldImage = $currentGift['imagem'] ?? null;
            
            // Processar upload de nova imagem se houver
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = uploadGiftImage($_FILES['imagem']);
                if ($uploadResult['success']) {
                    $imagemFilename = $uploadResult['filename'];
                    // Deletar imagem antiga se houver
                    if ($oldImage) {
                        deleteGiftImage($oldImage);
                    }
                } else {
                    $message = $uploadResult['error'];
                    $messageType = 'danger';
                    break;
                }
            }
            
            if ($id && $titulo && $valor > 0) {
                if (updateGift($id, $titulo, $valor, 0, $imagemFilename)) {
                    $message = 'Presente atualizado com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao atualizar presente.';
                    $messageType = 'danger';
                    // Restaurar imagem antiga se falhou
                    if ($imagemFilename) {
                        deleteGiftImage($imagemFilename);
                    }
                }
            } else {
                $message = 'Por favor, preencha todos os campos corretamente.';
                $messageType = 'warning';
                // Deletar nova imagem se validação falhou
                if ($imagemFilename) {
                    deleteGiftImage($imagemFilename);
                }
            }
            break;
            
        case 'delete_gift':
            $id = intval($_POST['gift_id'] ?? 0);
            
            if ($id) {
                // Buscar presente para deletar imagem
                $gift = getGiftById($id);
                
                if (deleteGift($id)) {
                    // Deletar imagem se houver
                    if (isset($gift['imagem']) && $gift['imagem']) {
                        deleteGiftImage($gift['imagem']);
                    }
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
?>

<?php include 'navbar.php'; ?>

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

        <!-- Cabeçalho da Página -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-center justify-content-lg-between align-items-center flex-column flex-lg-row ">
                    <div class="text-center text-lg-start">
                        <h2 class="admin-page-title">
                            <i class="fas fa-cogs me-2"></i>Gerenciar Presentes
                        </h2>
                        <p class="admin-page-subtitle text-muted">Adicione, edite e gerencie os presentes da lista</p>
                    </div>
                    <div>
                        <a href="<?php echo base_url('presentes'); ?>" target="_blank" class="btn btn-outline-primary admin-btn-outline">
                            <i class="fas fa-external-link-alt me-2"></i>Ver Lista Pública
                        </a>
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
                    <div class="col-12 col-md-12 col-lg-5 col-xl-6">
                        <label for="search" class="form-label">Buscar Presentes</label>
                        <input type="text" class="form-control admin-form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" placeholder="Digite o nome do presente...">
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select admin-form-control form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Disponível</option>
                            <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Comprado</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 col-xl-2 d-flex align-items-end">
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
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="action" value="add_gift">
                    <div class="col-12 col-lg-5">
                        <label for="titulo" class="form-label">Nome do Presente</label>
                        <input type="text" class="form-control admin-form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <label for="valor" class="form-label">Valor (R$)</label>
                        <input type="number" class="form-control admin-form-control" id="valor" name="valor" 
                               step="0.01" min="0" required>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="imagem" class="form-label">Imagem (opcional)</label>
                        <input type="file" class="form-control admin-form-control" id="imagem" name="imagem" 
                               accept="image/jpeg,image/jpg,image/png,image/webp,image/gif">
                        <small class="text-muted">JPG, PNG, WEBP ou GIF (max 5MB)</small>
                    </div>
                    <div class="col-12 col-lg-2 d-flex align-items-end">
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
                                    <th class="text-center"  style="width: 200px;">Ações</th>
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
                                        <td class="text-center" style="width: 200px;">
                                            <div class="d-flex gap-2 align-items-center" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary admin-btn-sm px-3 py-2" 
                                                        data-bs-toggle="modal" data-bs-target="#editModal<?php echo $gift['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="toggle_gift_status">
                                                    <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                    <button type="submit" class="btn btn-sm  px-3 py-2 btn-outline-<?php echo $gift['status'] ? 'warning' : 'success'; ?> admin-btn-sm">
                                                        <i class="fas fa-<?php echo $gift['status'] ? 'undo' : 'check'; ?>"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este presente?');">
                                                    <input type="hidden" name="action" value="delete_gift">
                                                    <input type="hidden" name="gift_id" value="<?php echo $gift['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger admin-btn-sm  px-3 py-2">
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
                                                <form method="POST" enctype="multipart/form-data">
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
                                                        <div class="mb-3">
                                                            <label for="edit_imagem_<?php echo $gift['id']; ?>" class="form-label">Imagem</label>
                                                            <?php if (!empty($gift['imagem'])): ?>
                                                                <div class="mb-2">
                                                                    <img src="<?php echo getGiftImageUrl($gift['imagem']); ?>" 
                                                                         alt="<?php echo htmlspecialchars($gift['titulo']); ?>" 
                                                                         style="max-width: 150px; border-radius: 8px;">
                                                                    <p class="text-muted small mb-0">Imagem atual</p>
                                                                </div>
                                                            <?php endif; ?>
                                                            <input type="file" class="form-control admin-form-control" 
                                                                   id="edit_imagem_<?php echo $gift['id']; ?>" name="imagem" 
                                                                   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif">
                                                            <small class="text-muted">Envie uma nova imagem para substituir (JPG, PNG, WEBP ou GIF, max 5MB)</small>
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
