<?php
/**
 * Header consolidado para páginas administrativas
 * Inclui HTML head, CSS e navbar comum
 */

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . base_url('admin/login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin'; ?> - Sistema de Presentes</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo base_url('assets/images/favicon.png'); ?>" type="image/png">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Customizado -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">

    <!-- CSS Customizado para navbar -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/admin.css'); ?>">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript Admin Centralizado -->
    <script src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
</head>

<body class="admin-body pb-5">
    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-xl navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand admin-navbar-brand" href="<?php echo base_url('admin'); ?>">
                <i class="fas fa-gift me-2"></i>
                <span class="d-none d-sm-inline">Admin - Sistema de Presentes</span>
                <span class="d-inline d-sm-none">Admin</span>
            </a>

            <!-- Botão toggle para mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu colapsável -->
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-0 me-lg-3">
                        <a class="nav-link" href="<?php echo base_url('admin/gifts'); ?>">
                            <i class="fas fa-cogs me-2"></i>
                            <span>Presentes</span>
                        </a>
                    </li>
                    <li class="nav-item me-0 me-lg-3">
                        <a class="nav-link" href="<?php echo base_url('admin/recados'); ?>">
                            <i class="fas fa-comments me-2"></i>Recados
                        </a>
                    </li>
                    <li class="nav-item me-0 me-lg-3">
                        <a class="nav-link" href="<?php echo base_url('admin/pix_transactions'); ?>">
                            <i class="fas fa-credit-card me-2"></i>
                            <span class="d-none d-lg-inline">PIX</span>
                            <span class="d-lg-none">PIX</span>
                        </a>
                    </li>
                    <li class="nav-item me-0 me-lg-3">
                        <a class="nav-link" href="<?php echo base_url('admin'); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-2"></i>
                            <span class="d-none d-md-inline"><?php echo $_SESSION['admin_email']; ?></span>
                            <span class="d-md-none">Perfil</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo base_url(); ?>" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Ver Site
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?php echo base_url('presentes'); ?>" target="_blank">
                                    <i class="fas fa-gift me-2"></i>Ver Lista de Presentes
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?php echo base_url('admin/logout'); ?>">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container mt-4">
