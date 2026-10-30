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
        body { 
            background-color: #f1f5f9; 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* NAVBAR PRINCIPAL DO PARCEIRO */
        .partner-navbar {
            background: linear-gradient(180deg, #0f172a 0%, #0b1120 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.25);
            padding: 0.5rem 0;
        }

        .partner-brand-badge {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
            white-space: nowrap;
        }

        /* NAVEGAÇÃO */
        .partner-nav {
            align-items: center;
        }

        .partner-nav .nav-link {
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.86rem;
            padding: 7px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }

        .partner-nav .nav-link i {
            font-size: 0.92rem;
            color: #64748b;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .partner-nav .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
            transform: translateY(-1px);
        }

        .partner-nav .nav-link:hover i {
            color: #cbd5e1;
            transform: scale(1.1);
        }

        .partner-nav .nav-link.active {
            color: #ffffff;
            background: rgba(220, 38, 38, 0.18);
            border-color: rgba(220, 38, 38, 0.35);
            font-weight: 600;
        }

        .partner-nav .nav-link.active i {
            color: #ef4444;
        }

        /* BOTÃO ESPECIAL: LANÇAR PONTOS */
        .partner-nav .nav-link-pontos {
            color: #fde047 !important;
            background: rgba(234, 179, 8, 0.1);
            border-color: rgba(234, 179, 8, 0.25);
            font-weight: 600;
        }

        .partner-nav .nav-link-pontos i {
            color: #eab308 !important;
        }

        .partner-nav .nav-link-pontos:hover {
            background: rgba(234, 179, 8, 0.2) !important;
            border-color: rgba(234, 179, 8, 0.45) !important;
            color: #fef08a !important;
            box-shadow: 0 0 12px rgba(234, 179, 8, 0.2);
        }

        /* BOTÃO ESPECIAL: LINECASH PDV */
        .partner-nav .nav-link-linecash {
            color: #4ade80 !important;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.12) 0%, rgba(16, 185, 129, 0.18) 100%);
            border: 1px solid rgba(34, 197, 94, 0.35);
            font-weight: 700;
            box-shadow: 0 2px 10px rgba(34, 197, 94, 0.15);
        }

        .partner-nav .nav-link-linecash i {
            color: #22c55e !important;
        }

        .partner-nav .nav-link-linecash:hover,
        .partner-nav .nav-link-linecash.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #16a34a 0%, #059669 100%) !important;
            border-color: #22c55e !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
        }

        .partner-nav .nav-link-linecash:hover i,
        .partner-nav .nav-link-linecash.active i {
            color: #ffffff !important;
        }

        /* AÇÕES DIREITA */
        .btn-nav-portal {
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            font-size: 0.82rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 9999px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .btn-nav-portal:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-nav-logout {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 9999px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .btn-nav-logout:hover {
            color: #ffffff;
            background: #dc2626;
            border-color: #dc2626;
            box-shadow: 0 2px 10px rgba(220, 38, 38, 0.35);
        }

        /* MOBILE ADJUSTMENTS */
        @media (max-width: 991.98px) {
            .partner-nav {
                padding: 12px 0;
                gap: 6px;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                margin-top: 10px;
            }
            .partner-nav .nav-link {
                width: 100%;
                justify-content: flex-start;
                padding: 10px 14px;
            }
            .partner-actions {
                padding-top: 10px;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                margin-top: 6px;
                flex-direction: column;
                width: 100%;
            }
            .partner-actions a {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
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
    <nav class="navbar navbar-expand-lg partner-navbar sticky-top">
        <div class="container-fluid px-xl-4 px-lg-3 px-2">
            <a class="navbar-brand d-flex align-items-center gap-2 me-lg-3" href="<?= base_url('parceiro/dashboard') ?>">
                <img src="<?= base_url('assets/images/logo_branco.png') ?>" alt="Boca Santa" style="height: 38px; width: auto; object-fit: contain;">
                <span class="partner-brand-badge">Área do Parceiro</span>
            </a>
            
            <button class="navbar-toggler border-0 text-white shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#partnerNavbar" aria-controls="partnerNavbar" aria-expanded="false" aria-label="Navegação">
                <i class="fa-solid fa-bars fs-5"></i>
            </button>

            <div class="collapse navbar-collapse" id="partnerNavbar">
                <ul class="navbar-nav mx-auto partner-nav my-2 my-lg-0 gap-1">
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/dashboard') ?>" class="nav-link <?= uri_string() === 'parceiro/dashboard' ? 'active' : '' ?>">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Início</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/ofertas') ?>" class="nav-link <?= str_contains(uri_string(), 'parceiro/ofertas') ? 'active' : '' ?>">
                            <i class="fa-solid fa-tags"></i>
                            <span>Minhas Ofertas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/perfil') ?>" class="nav-link <?= str_contains(uri_string(), 'parceiro/perfil') ? 'active' : '' ?>">
                            <i class="fa-solid fa-building"></i>
                            <span>Meus Dados & Empresa</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/fidelidade') ?>" class="nav-link <?= (uri_string() === 'parceiro/fidelidade' || (str_contains(uri_string(), 'parceiro/fidelidade') && !str_contains(uri_string(), 'lancamentos'))) ? 'active' : '' ?>">
                            <i class="fa-solid fa-id-card"></i>
                            <span>Fidelidade</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/fidelidade/lancamentos') ?>" class="nav-link nav-link-pontos <?= str_contains(uri_string(), 'parceiro/fidelidade/lancamentos') ? 'active' : '' ?>">
                            <i class="fa-solid fa-stamp"></i>
                            <span>Lançar Pontos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('parceiro/linecash') ?>" class="nav-link nav-link-linecash <?= str_contains(uri_string(), 'parceiro/linecash') ? 'active' : '' ?>">
                            <i class="fa-solid fa-coins"></i>
                            <span>LineCash PDV</span>
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 partner-actions ms-lg-2">
                    <a href="<?= base_url() ?>" target="_blank" class="btn btn-nav-portal d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-globe"></i>
                        <span>Ver Portal</span>
                    </a>
                    <a href="<?= base_url('parceiro/logout') ?>" class="btn btn-nav-logout d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Sair</span>
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
