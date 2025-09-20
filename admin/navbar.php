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
    
    <style>
        /* Melhorias para navbar responsiva */
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        @media (max-width: 991.98px) {
            .navbar-collapse {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .navbar-nav .nav-item:last-child .nav-link {
                border-bottom: none;
            }
            
            .dropdown-menu {
                position: static !important;
                transform: none !important;
                border: none;
                box-shadow: none;
                background: rgba(255, 255, 255, 0.1);
                margin-top: 0.5rem;
            }
            
            .dropdown-item {
                color: rgba(255, 255, 255, 0.85);
                padding: 0.5rem 1rem;
            }
            
            .dropdown-item:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white;
            }
        }
    </style>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
