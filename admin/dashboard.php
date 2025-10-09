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
    }
}
$stats = calculateGiftStats();

// Adicionar estatísticas PIX
$stats['total_pix_transactions'] = countPixTransactionsByStatus();
$stats['confirmed_pix_transactions'] = countPixTransactionsByStatus('confirmado');
$stats['pre_confirmed_pix_transactions'] = countPixTransactionsByStatus('pre_confirmado');
$stats['initiated_pix_transactions'] = countPixTransactionsByStatus('iniciado');
$stats['total_pix_amount'] = getTotalConfirmedPixAmount();
?>

<?php include 'navbar.php'; ?>

        <!-- Cabeçalho da Página -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-center justify-content-lg-between align-items-center flex-column flex-lg-row ">
                    <div class="text-center text-lg-start">
                        <h2 class="admin-page-title">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </h2>
                        <p class="admin-page-subtitle text-muted">Visão geral do sistema de presentes</p>
                    </div>
                    <div>
                        <a href="<?php echo base_url('admin/gifts'); ?>" class="btn btn-primary admin-btn-primary">
                            <i class="fas fa-cogs me-2"></i>Gerenciar Presentes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-gift fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['total_gifts']; ?></div>
                        <div>Total de Presentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['purchased_gifts']; ?></div>
                        <div>Compras</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <div class="admin-stats-number">R$ <?php echo number_format($stats['total_value'], 2, ',', '.'); ?></div>
                        <div>Valor Total (Soma dos presentes)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas PIX -->
        <div class="row mb-4">
            <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['total_pix_transactions']; ?></div>
                        <div>Transações PIX</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['confirmed_pix_transactions']; ?></div>
                        <div>PIX Confirmados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['pre_confirmed_pix_transactions']; ?></div>
                        <div>PIX Pré Confirmados</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <div class="admin-stats-number">R$ <?php echo number_format($stats['total_pix_amount'] ?? 0, 2, ',', '.'); ?></div>
                        <div>Total PIX Confirmado</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card admin-stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <div class="admin-stats-number"><?php echo $stats['total_recados']; ?></div>
                        <div>Total de Recados</div>
                    </div>
                </div>
            </div>
        </div>



<?php include 'footer.php'; ?>
