<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title><?= esc($title ?? 'Admin') ?> | Boca Santa AdminLTE</title>

    <!-- Google Font: Source Sans Pro & Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AdminLTE 3.2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <style>
        .brand-link { background: #1e293b !important; }
        .main-sidebar { background: #0f172a !important; }
        .nav-sidebar .nav-link.active { background-color: #dc2626 !important; color: #fff !important; }
        .btn-primary { background-color: #dc2626; border-color: #dc2626; }
        .btn-primary:hover { background-color: #b91c1c; border-color: #b91c1c; }
        .badge-linefast { background-color: #2563eb; color: #fff; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= base_url() ?>" target="_blank" class="nav-link"><i class="fas fa-external-link-alt me-1"></i> Ver Portal</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user-circle fs-5"></i>
                    <span class="d-none d-md-inline ms-1 fw-bold"><?= esc(session()->get('admin_nome') ?? 'Administrador') ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">Sessão Ativa</span>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('admin/logout') ?>" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Encerrar Sessão
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="brand-link text-center">
            <span class="brand-text font-weight-bold text-white"><i class="fas fa-bullhorn text-danger mr-1"></i> BOCA SANTA</span>
        </a>

        <div class="sidebar">
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= (uri_string() === 'admin/dashboard' || uri_string() === 'admin') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/ofertas') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/ofertas') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Ofertas & Produtos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/parceiros') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/parceiros') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-store"></i>
                            <p>Parceiros Comerciais</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/linefast') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/linefast') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-bolt text-warning"></i>
                            <p>Integração Linefast</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/lgpd') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/lgpd') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-user-shield text-info"></i>
                            <p>Privacidade & LGPD</p>
                        </a>
                    </li>
                    <li class="nav-header">PORTAL & CONFIGS</li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/categorias') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/categorias') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>Categorias</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/banners') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/banners') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-image"></i>
                            <p>Banners Publicitários</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/cidades') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/cidades') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>Cidades Atendidas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/estados') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/estados') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-flag"></i>
                            <p>Estados (UF)</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/redirecionamentos') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/redirecionamentos') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-directions"></i>
                            <p>Redirecionamentos SEO</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('admin/administradores') ?>" class="nav-link <?= str_contains(uri_string(), 'admin/administradores') ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Administradores</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-dark"><?= esc($title ?? 'Painel de Controle') ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            CodeIgniter 4 + PHP 8.2 &bull; AdminLTE 3
        </div>
        <strong>&copy; <?= date('Y') ?> <a href="<?= base_url() ?>" class="text-danger">Boca Santa Ofertas</a>.</strong> Todos os direitos reservados.
    </footer>
</div>

<!-- jQuery & Bootstrap 4 (AdminLTE Bundle) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        if ($('.datatable').length) {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
                }
            });
        }
    });
</script>
</body>
</html>
