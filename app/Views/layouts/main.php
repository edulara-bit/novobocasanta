<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title><?= esc($seo['title'] ?? 'Boca Santa Ofertas') ?></title>
    <meta name="description" content="<?= esc($seo['description'] ?? '') ?>">
    <meta name="keywords" content="<?= esc($seo['keywords'] ?? '') ?>">
    <link rel="canonical" href="<?= esc($seo['canonical'] ?? current_url()) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="<?= esc($seo['og']['type'] ?? 'website') ?>">
    <meta property="og:title" content="<?= esc($seo['og']['title'] ?? '') ?>">
    <meta property="og:description" content="<?= esc($seo['og']['description'] ?? '') ?>">
    <meta property="og:url" content="<?= esc($seo['og']['url'] ?? current_url()) ?>">
    <meta property="og:image" content="<?= esc($seo['og']['image'] ?? '') ?>">
    <meta property="og:site_name" content="Boca Santa Ofertas">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($seo['twitter']['title'] ?? '') ?>">
    <meta name="twitter:description" content="<?= esc($seo['twitter']['description'] ?? '') ?>">
    <meta name="twitter:image" content="<?= esc($seo['twitter']['image'] ?? '') ?>">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Bocasanta Theme CSS -->
    <link href="<?= base_url('assets/css/bocasanta-theme.css') ?>" rel="stylesheet">

    <?php if (!empty($seo['schema_json'])): ?>
    <!-- Structured Data (Schema.org) -->
    <script type="application/ld+json">
        <?= $seo['schema_json'] ?>
    </script>
    <?php endif; ?>

    <script>
        window.BocaSantaConfig = {
            baseUrl: '<?= base_url() ?>',
            cidadeAtiva: '<?= esc($cidadeAtual['cid_url'] ?? 'piracicaba') ?>'
        };
    </script>
</head>
<body>

    <!-- TOP BAR -->
    <div class="bocasanta-topbar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="fa-solid fa-location-dot text-danger me-1"></i> Você está vendo ofertas de: <strong><?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?> - SP</strong></span>
                <a href="#modalSelectCity" data-bs-toggle="modal" class="badge bg-secondary text-white text-decoration-none">Alterar Cidade</a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('anuncie') ?>"><i class="fa-solid fa-bullhorn me-1 text-warning"></i> Anuncie Conosco</a>
                <a href="<?= base_url('parceiro/login') ?>"><i class="fa-solid fa-store me-1"></i> Painel do Parceiro</a>
                <a href="<?= base_url('admin/login') ?>"><i class="fa-solid fa-lock me-1"></i> Admin</a>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar navbar-expand-lg bocasanta-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center py-0" href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Boca Santa Ofertas" class="logo-img" style="height: 57px; width: auto; object-fit: contain;">
            </a>

            <!-- Mobile City & Toggle -->
            <div class="d-flex d-lg-none align-items-center gap-2">
                <button class="city-badge-btn" data-bs-toggle="modal" data-bs-target="#modalSelectCity">
                    <i class="fa-solid fa-location-dot text-danger"></i> <?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?>
                </button>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <i class="fa-solid fa-bars fs-4"></i>
                </button>
            </div>

            <!-- SEARCH BAR -->
            <div class="d-none d-lg-flex flex-grow-1 justify-content-center px-4">
                <form action="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/busca') ?>" method="GET" class="main-search-wrapper">
                    <input type="text" name="q" id="mainSearchInput" class="main-search-input" placeholder="O que você procura hoje? Ex: Impressora, Pizza, Academia, Pneu..." value="<?= esc($buscaQuery ?? '') ?>">
                    <button type="submit" class="main-search-btn" title="Buscar">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <!-- RIGHT ACTIONS -->
            <div class="collapse navbar-collapse flex-grow-0" id="navbarMain">
                <!-- Mobile Search -->
                <div class="d-lg-none my-3">
                    <form action="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/busca') ?>" method="GET" class="main-search-wrapper">
                        <input type="text" name="q" class="main-search-input" placeholder="Buscar ofertas..." value="<?= esc($buscaQuery ?? '') ?>">
                        <button type="submit" class="main-search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="city-badge-btn d-none d-lg-inline-flex" data-bs-toggle="modal" data-bs-target="#modalSelectCity">
                        <i class="fa-solid fa-location-dot text-danger"></i> <?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?> <i class="fa-solid fa-chevron-down ms-1 small text-muted"></i>
                    </button>

                    <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/parceiros') ?>" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                        <i class="fa-solid fa-store me-1"></i> Parceiros
                    </a>

                    <a href="<?= base_url('anuncie') ?>" class="btn btn-danger rounded-pill px-3 fw-bold shadow-sm d-none d-xl-inline-flex">
                        <i class="fa-solid fa-bullhorn me-1"></i> Anuncie
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CATEGORIES QUICK BAR -->
    <?php if (!empty($categoriasBar)): ?>
    <div class="category-nav-bar">
        <div class="container position-relative d-flex align-items-center gap-2">
            <button type="button" class="cat-nav-arrow cat-nav-prev d-none d-md-flex" onclick="document.getElementById('categoryScrollContainer').scrollBy({left: -220, behavior: 'smooth'})" aria-label="Anterior">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <div class="category-scroll-container d-flex align-items-center gap-2" id="categoryScrollContainer">
                <a href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="category-nav-item <?= empty($categoriaAtiva) ? 'active' : '' ?>">
                    <i class="fa-solid fa-fire"></i> Todos os Destaques
                </a>
                <?php foreach ($categoriasBar as $cat): ?>
                    <?php if (!empty($cat->cat_titulo) && !empty($cat->cat_url)): ?>
                        <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/categoria/' . $cat->cat_url) ?>" class="category-nav-item <?= ($categoriaAtiva ?? '') === $cat->cat_url ? 'active' : '' ?>">
                            <i class="fa-solid <?= $cat->getIconeClass() ?> <?= $cat->getIconeCorClass() ?> me-1"></i> <?= esc($cat->cat_titulo) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <button type="button" class="cat-nav-arrow cat-nav-next d-none d-md-flex" onclick="document.getElementById('categoryScrollContainer').scrollBy({left: 220, behavior: 'smooth'})" aria-label="Próximo">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAIN CONTENT -->
    <main class="py-4">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- FLOATING CTA: ANUNCIE AQUI / GOOGLE LEADS -->
    <div class="d-none d-md-block position-fixed bottom-0 start-0 m-4 z-3" id="floatingGoogleCta" style="max-width: 320px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark text-white p-3 border border-secondary">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-brands fa-google me-1"></i> ANUNCIE AQUI</span>
                <button type="button" class="btn btn-link text-white-50 p-0 border-0 text-decoration-none shadow-none" id="btnCloseFloatingCta" aria-label="Fechar" onclick="var cta = document.getElementById('floatingGoogleCta'); if(cta){ cta.remove(); } sessionStorage.setItem('bocasanta_hide_floating_cta', '1');" style="line-height: 1; font-size: 1.25rem;">
                    <i class="fa-solid fa-xmark text-white"></i>
                </button>
            </div>
            <h6 class="fw-bold mb-1">Encontrou este produto no Google?</h6>
            <p class="small text-white-50 mb-3">Seu cliente também pode encontrar <strong>Você</strong>! Cadastre-se e turbine suas vendas.</p>
            <a href="<?= base_url('anuncie') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold w-100 shadow-sm">
                Cadastre-se Já &bull; É Fácil e Rápido!
            </a>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bocasanta-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <img src="<?= base_url('assets/images/logo_branco.png') ?>" alt="Boca Santa Ofertas" height="42" class="mb-3" onerror="this.src='<?= base_url('assets/images/logobranco.png') ?>'">
                    <p class="small text-secondary">
                        O maior portal de ofertas, descontos exclusivos e compras locais de Piracicaba e região. Conectamos consumidores às melhores oportunidades comerciais com rapidez e segurança.
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="https://facebook.com" class="text-white fs-5" target="_blank"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://instagram.com" class="text-white fs-5" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://whatsapp.com" class="text-white fs-5" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h5>Principais Cidades</h5>
                    <ul>
                        <?php if (!empty($principaisCidades)): ?>
                            <?php foreach ($principaisCidades as $cid): ?>
                                <li><a href="<?= base_url($cid['cid_url']) ?>"><?= esc($cid['cid_nome']) ?> - SP</a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="<?= base_url('piracicaba') ?>">Piracicaba - SP</a></li>
                        <?php endif; ?>
                        <li class="mt-2">
                            <a href="#modalSelectCity" data-bs-toggle="modal" class="text-danger fw-bold">
                                <i class="fa-solid fa-location-dot me-1"></i> Ver todas as cidades
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h5>Parceiros & Linefast</h5>
                    <ul>
                        <li><a href="<?= base_url('anuncie') ?>" class="text-warning fw-bold"><i class="fa-solid fa-bullhorn me-1"></i> Anuncie Conosco</a></li>
                        <li><a href="<?= base_url('parceiro/login') ?>"><i class="fa-solid fa-store me-1"></i> Painel do Parceiro</a></li>
                        <li><a href="https://linefast.com.br" target="_blank">Sobre o Linefast <i class="fa-solid fa-arrow-up-right-from-square small"></i></a></li>
                        <li><a href="<?= base_url('termos') ?>">Termos de Adesão</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Privacidade & LGPD</h5>
                    <ul>
                        <li><a href="<?= base_url('politica-privacidade') ?>">Política de Privacidade</a></li>
                        <li><a href="<?= base_url('termos') ?>">Termos de Uso</a></li>
                        <li><a href="<?= base_url('lgpd/solicitar-dados') ?>">Portal do Titular (Art. 18 LGPD)</a></li>
                        <li><a href="#modalLgpdPreferences" data-bs-toggle="modal">Configurar Cookies</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-secondary">
                <span>&copy; <?= date('Y') ?> Boca Santa Ofertas. Todos os direitos reservados.</span>
                <span>Desenvolvido com tecnologia de alta performance &bull; CodeIgniter 4</span>
            </div>
        </div>
    </footer>

    <!-- MODAL SELETOR DE CIDADES -->
    <div class="modal fade" id="modalSelectCity" tabindex="-1" aria-labelledby="modalSelectCityLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalSelectCityLabel"><i class="fa-solid fa-location-dot text-danger me-2"></i> Selecione sua Cidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="text-muted small mb-3">Escolha a cidade para ver promoções e comércios locais perto de você:</p>
                    <div class="list-group list-group-flush">
                        <?php if (!empty($todasCidades)): ?>
                            <?php foreach ($todasCidades as $c): ?>
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 select-city-trigger" data-city-slug="<?= esc($c['cid_url']) ?>">
                                    <span class="fw-semibold fs-6"><?= esc($c['cid_nome']) ?> - SP</span>
                                    <i class="fa-solid fa-chevron-right text-muted"></i>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LGPD COOKIE CONSENT BANNER -->
    <div class="lgpd-cookie-banner" id="lgpdCookieBanner">
        <h6><i class="fa-solid fa-shield-halved text-warning"></i> Privacidade & Cookies</h6>
        <p>
            Utilizamos cookies essenciais e tecnologias semelhantes em conformidade com a <strong>LGPD</strong> para garantir a melhor experiência no portal Boca Santa Ofertas.
        </p>
        <div class="lgpd-actions">
            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" id="btnLgpdAcceptAll">Aceitar Todos</button>
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="btnLgpdReject">Apenas Essenciais</button>
            <button type="button" class="btn btn-sm btn-link text-white-50 text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalLgpdPreferences">Personalizar</button>
        </div>
    </div>

    <!-- MODAL LGPD PREFERENCES -->
    <div class="modal fade" id="modalLgpdPreferences" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-cookie-bite text-warning me-2"></i> Preferências de Privacidade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="lgpdConsentEssential" checked disabled>
                            <label class="form-check-label fw-bold" for="lgpdConsentEssential">Cookies Essenciais</label>
                        </div>
                        <small class="text-muted d-block">Necessários para o funcionamento básico, segurança e navegação no site.</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="lgpdConsentAnalytics" checked>
                            <label class="form-check-label fw-bold" for="lgpdConsentAnalytics">Cookies Analíticos</label>
                        </div>
                        <small class="text-muted d-block">Permitem mensurar audiência de forma anônima para melhorar as funcionalidades do portal.</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="lgpdConsentMarketing">
                            <label class="form-check-label fw-bold" for="lgpdConsentMarketing">Cookies de Publicidade & Parceiros</label>
                        </div>
                        <small class="text-muted d-block">Utilizados para exibir ofertas personalizadas e integração com o Linefast.</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-danger rounded-pill fw-bold" id="btnLgpdSaveCustom">Salvar Preferências</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bocasanta App JS -->
    <script src="<?= base_url('assets/js/bocasanta-app.js') ?>"></script>
</body>
</html>
