<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title><?= esc($title ?? 'Painel do Parceiro') ?> | Boca Santa</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Theme CSS -->
    <link href="<?= base_url('assets/css/bocasanta-theme.css') ?>" rel="stylesheet">
    <style>
        body { background-color: #f1f5f9; }
        .partner-nav .nav-link {
            color: #475569;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 9999px;
            transition: all 0.2s ease;
        }
        .partner-nav .nav-link:hover, .partner-nav .nav-link.active {
            background-color: #dc2626;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- IMPERSONATION BANNER SE LOGADO VIA ADMIN -->
    <?php if (session()->get('impersonated_by_adm')): ?>
        <div class="bg-warning text-dark py-2 px-3 text-center fw-bold small">
            <i class="fa-solid fa-user-shield me-1"></i> Você está acessando este painel no modo administrativo impersonado por: <strong><?= esc(session()->get('impersonated_by_adm')) ?></strong>. 
            <a href="<?= base_url('admin/parceiros') ?>" class="btn btn-dark btn-xs ms-2 rounded-pill px-2 py-0 text-decoration-none">Voltar ao Admin</a>
        </div>
    <?php endif; ?>

    <!-- NAVBAR DO PARCEIRO -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('parceiro/dashboard') ?>">
                <img src="<?= base_url('assets/images/logo_branco.png') ?>" alt="Boca Santa" style="height: 40px; width: auto; object-fit: contain;">
                <span class="badge bg-danger ms-1">Área do Parceiro</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#partnerNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="partnerNavbar">
                <ul class="navbar-nav mx-auto partner-nav my-2 my-lg-0 gap-1">
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/dashboard') ?>" class="nav-link <?= uri_string() === 'parceiro/dashboard' ? 'active' : '' ?>">
                            <i class="fa-solid fa-gauge me-1"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/ofertas') ?>" class="nav-link <?= str_contains(uri_string(), 'parceiro/ofertas') ? 'active' : '' ?>">
                            <i class="fa-solid fa-tags me-1"></i> Minhas Ofertas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/perfil') ?>" class="nav-link <?= str_contains(uri_string(), 'parceiro/perfil') ? 'active' : '' ?>">
                            <i class="fa-solid fa-building me-1"></i> Meus Dados & Empresa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/fidelidade') ?>" class="nav-link <?= uri_string() === 'parceiro/fidelidade' ? 'active' : '' ?>">
                            <i class="fa-solid fa-id-card me-1"></i> Fidelidade
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/fidelidade/lancamentos') ?>" class="nav-link text-warning fw-bold <?= str_contains(uri_string(), 'parceiro/fidelidade/lancamentos') ? 'active' : '' ?>">
                            <i class="fa-solid fa-stamp me-1"></i> Lançar Pontos
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?= base_url() ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-globe me-1"></i> Ver Portal
                    </a>
                    <a href="<?= base_url('parceiro/logout') ?>" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="container py-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
