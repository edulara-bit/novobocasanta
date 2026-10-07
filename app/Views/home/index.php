<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">

    <!-- HERO BANNERS CAROUSEL -->
    <div class="row mb-5">
        <div class="col-12">
            <div id="carouselHeroBanners" class="carousel slide hero-banner-card overflow-hidden rounded-4 shadow-sm" data-bs-ride="carousel">
                <?php if (!empty($banners)): ?>
                    <div class="carousel-indicators">
                        <?php foreach ($banners as $idx => $b): ?>
                            <button type="button" data-bs-target="#carouselHeroBanners" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-current="<?= $idx === 0 ? 'true' : 'false' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($banners as $idx => $b): ?>
                            <?php
                                $fundoImg = $b['ban_fundo_imagem'] ?? '';
                                if (!empty($fundoImg) && !str_starts_with($fundoImg, 'http')) {
                                    $fundoImg = base_url($fundoImg);
                                }

                                $bgStyle = '';
                                $tipoFundo = $b['ban_tipo_fundo'] ?? 'degrade';
                                if ($tipoFundo === 'cor') {
                                    $bgStyle = 'background-color: ' . (!empty($b['ban_fundo_cor']) ? $b['ban_fundo_cor'] : '#0f172a') . ';';
                                } elseif ($tipoFundo === 'imagem' && !empty($fundoImg)) {
                                    $bgStyle = "background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('" . esc($fundoImg) . "') center/cover no-repeat;";
                                } else {
                                    $degrade = !empty($b['ban_fundo_degrade']) ? $b['ban_fundo_degrade'] : 'linear-gradient(90deg, #991b1b 0%, #dc2626 50%, #ea580c 100%)';
                                    $bgStyle = 'background: ' . $degrade . ';';
                                }

                                $btnLink = $b['ban_botao_link'] ?? '#';
                                if (!empty($btnLink) && !str_starts_with($btnLink, 'http') && !str_starts_with($btnLink, '#')) {
                                    $btnLink = base_url($btnLink);
                                }

                                $rightImg = $b['ban_imagem_direita'] ?? '';
                                if (!empty($rightImg) && !str_starts_with($rightImg, 'http')) {
                                    $rightImg = base_url($rightImg);
                                }
                                $bannerTitulo = $b['ban_titulo'] ?? 'Boca Santa Ofertas';
                            ?>
                            <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                <div class="p-4 p-md-5 d-flex align-items-center" style="min-height: 330px; <?= $bgStyle ?>">
                                    <div class="row w-100 align-items-center g-4">
                                        <div class="<?= !empty($rightImg) ? 'col-12 col-md-7 col-lg-7' : 'col-12' ?>">
                                            <?php if (!empty($b['ban_badge'])): ?>
                                                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3 d-inline-block shadow-sm">
                                                    <?= esc($b['ban_badge']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <h1 class="display-6 display-md-5 fw-extrabold text-white mb-3 text-shadow">
                                                <?= esc($bannerTitulo) ?>
                                            </h1>
                                            <?php if (!empty($b['ban_descricao'])): ?>
                                                <p class="lead text-white-50 mb-4 <?= !empty($rightImg) ? '' : 'col-lg-8' ?>">
                                                    <?= esc($b['ban_descricao']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($b['ban_botao_texto'])): ?>
                                                <div>
                                                    <a href="<?= esc($btnLink) ?>" class="btn btn-light btn-lg fw-bold rounded-pill px-4 text-dark shadow">
                                                        <?= esc($b['ban_botao_texto']) ?> <i class="fa-solid fa-arrow-right ms-1 small"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($rightImg)): ?>
                                            <div class="col-12 col-md-5 col-lg-5 text-center text-md-end">
                                                <img src="<?= esc($rightImg) ?>" alt="<?= esc($bannerTitulo) ?>" class="img-fluid banner-right-image" style="max-height: 280px; max-width: 100%; object-fit: contain; filter: drop-shadow(0 15px 25px rgba(0,0,0,0.3));">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselHeroBanners" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#carouselHeroBanners" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#carouselHeroBanners" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="p-4 p-md-5 d-flex flex-column justify-content-center" style="min-height: 320px; background: linear-gradient(90deg, #991b1b 0%, #dc2626 50%, #ea580c 100%);">
                                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill align-self-start mb-3">🔥 SUPER OFERTAS EM <?= esc(mb_strtoupper($cidadeAtual['cid_nome'] ?? 'PIRACICABA')) ?></span>
                                <h1 class="display-5 fw-extrabold text-white mb-3">Economize até 70% no comércio local</h1>
                                <p class="lead text-white-50 mb-4 col-lg-8">Descubra restaurantes, serviços automotivos, informática, saúde, beleza e produtos com os melhores preços da cidade.</p>
                                <div>
                                    <a href="#ofertasDestaque" class="btn btn-light btn-lg fw-bold rounded-pill px-4 text-danger shadow-sm">Ver Ofertas</a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="p-4 p-md-5 d-flex flex-column justify-content-center" style="min-height: 320px; background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%);">
                                <span class="badge bg-light text-primary fw-bold px-3 py-2 rounded-pill align-self-start mb-3"><i class="fa-solid fa-cart-shopping me-1"></i> INTEGRAÇÃO LINEFAST</span>
                                <h2 class="display-5 fw-extrabold text-white mb-3">Compre direto dos parceiros no Linefast</h2>
                                <p class="lead text-white-50 mb-4 col-lg-8">Produtos em estoque com compra em 1 clique e envio rápido diretamente pelo carrinho do parceiro.</p>
                                <div>
                                    <a href="#secaoLinefast" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 text-dark shadow-sm">Explorar Produtos Linefast</a>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="p-4 p-md-5 d-flex flex-column justify-content-center" style="min-height: 320px; background: linear-gradient(90deg, #065f46 0%, #059669 50%, #10b981 100%);">
                                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill align-self-start mb-3">⭐ CLUBE DE PARCEIROS</span>
                                <h2 class="display-5 fw-extrabold text-white mb-3">Conheça as melhores empresas da sua região</h2>
                                <p class="lead text-white-50 mb-4 col-lg-8">Comércios, lojas e prestadores de serviços de confiança com contato direto pelo WhatsApp.</p>
                                <div>
                                    <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/parceiros') ?>" class="btn btn-light btn-lg fw-bold rounded-pill px-4 text-success shadow-sm">Conhecer Parceiros</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselHeroBanners" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselHeroBanners" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- CATEGORIES GRID COM ÍCONES ESPECÍFICOS -->
    <?php if (!empty($categoriasPrincipais)): ?>
    <div class="mb-5">
        <div class="section-header">
            <h2 class="section-title">Navegue por Categorias</h2>
        </div>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
            <?php foreach ($categoriasPrincipais as $cat): ?>
                <?php if (!empty($cat->cat_titulo) && !empty($cat->cat_url)): ?>
                <div class="col">
                    <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/categoria/' . $cat->cat_url) ?>" class="card h-100 text-center border-0 shadow-sm rounded-4 p-3 text-decoration-none text-dark bg-white category-hover-card">
                        <div class="mb-2 fs-2">
                            <i class="fa-solid <?= $cat->getIconeClass() ?> <?= $cat->getIconeCorClass() ?>"></i>
                        </div>
                        <span class="fw-bold small"><?= esc($cat->cat_titulo) ?></span>
                    </a>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- OFERTAS EM DESTAQUE -->
    <div class="mb-5" id="ofertasDestaque">
        <div class="section-header">
            <div>
                <h2 class="section-title">Ofertas em Destaque</h2>
                <p class="text-muted small mb-0">As melhores oportunidades selecionadas para você em <?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?></p>
            </div>
            <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/busca?destaque=1') ?>" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold">
                Ver Todas <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php if (!empty($ofertasDestaque)): ?>
                <?php foreach ($ofertasDestaque as $oferta): ?>
                    <div class="col">
                        <div class="offer-card">
                            <div class="offer-card-media">
                                <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>">
                                    <img src="<?= $oferta->getImagemUrl() ?>" alt="<?= esc($oferta->pro_titulo) ?>" loading="lazy">
                                </a>
                                <?php if ($oferta->getPercentualDesconto() > 0): ?>
                                    <span class="offer-badge-discount">-<?= $oferta->getPercentualDesconto() ?>% OFF</span>
                                <?php endif; ?>
                                <?php if ($oferta->isLinefast()): ?>
                                    <span class="offer-badge-linefast"><i class="fa-solid fa-bolt"></i> Linefast</span>
                                <?php endif; ?>
                            </div>
                            <div class="offer-card-body">
                                <span class="offer-partner-name">
                                    <i class="fa-solid fa-store text-danger"></i> <?= esc($oferta->par_nome ?? 'Parceiro Boca Santa') ?>
                                </span>
                                <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="offer-card-title">
                                    <?= esc($oferta->pro_titulo) ?>
                                </a>
                                <div class="offer-price-box">
                                    <div>
                                        <?php if ($oferta->hasPrecoDe()): ?>
                                            <div class="price-original"><?= $oferta->getPrecoOriginalFormatado() ?></div>
                                        <?php endif; ?>
                                        <div class="price-current <?= !$oferta->hasPreco() ? 'fs-6 text-muted' : '' ?>">
                                            <?= $oferta->getPrecoVendaFormatado() ?>
                                        </div>
                                    </div>
                                    <?php if ($oferta->isLinefast()): ?>
                                        <a href="<?= $oferta->getLinefastCartUrl() ?>" class="btn-linefast-direct" target="_blank" title="Comprar direto no Linefast">
                                            <i class="fa-solid fa-cart-shopping"></i> Comprar
                                        </a>
                                    <?php elseif (!$oferta->hasPreco()): ?>
                                        <a href="<?= $oferta->getWhatsappConsultaLink($oferta->par_whatsapp ?? '') ?>" class="btn btn-sm btn-success rounded-pill fw-bold" target="_blank">
                                            <i class="fa-brands fa-whatsapp"></i> Consultar
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn-offer-action">
                                            Ver <i class="fa-solid fa-chevron-right small"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Nenhuma oferta em destaque no momento para esta cidade.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- SEÇÃO PRODUTOS LINEFAST -->
    <?php if (!empty($produtosLinefast)): ?>
    <div class="mb-5 p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;" id="secaoLinefast">
        <div class="section-header mb-3">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill mb-2"><i class="fa-solid fa-bolt me-1"></i> COMPRA ONLINE LINEFAST</span>
                <h2 class="section-title text-primary">Produtos Próprios & Estoque dos Parceiros</h2>
                <p class="text-muted small mb-0">Compre online e receba rápido direto do parceiro via Linefast</p>
            </div>
            <a href="https://linefast.com.br" target="_blank" class="btn btn-sm btn-primary rounded-pill fw-bold">
                Conheça o Linefast <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php foreach ($produtosLinefast as $prod): ?>
                <div class="col">
                    <div class="offer-card bg-white">
                        <div class="offer-card-media">
                            <a href="<?= $prod->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>">
                                <img src="<?= $prod->getImagemUrl() ?>" alt="<?= esc($prod->pro_titulo) ?>" loading="lazy">
                            </a>
                            <span class="offer-badge-linefast"><i class="fa-solid fa-bolt"></i> Estoque</span>
                        </div>
                        <div class="offer-card-body">
                            <span class="offer-partner-name text-primary">
                                <i class="fa-solid fa-store"></i> <?= esc($prod->par_nome ?? 'Parceiro Linefast') ?>
                            </span>
                            <a href="<?= $prod->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="offer-card-title">
                                <?= esc($prod->pro_titulo) ?>
                            </a>
                            <div class="offer-price-box">
                                <div>
                                    <div class="price-current text-primary"><?= $prod->getPrecoVendaFormatado() ?></div>
                                </div>
                                <a href="<?= $prod->getLinefastCartUrl() ?>" class="btn-linefast-direct" target="_blank">
                                    <i class="fa-solid fa-cart-shopping"></i> Comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAIS OFERTAS / ÚLTIMAS ADICIONADAS -->
    <div class="mb-5">
        <div class="section-header">
            <div>
                <h2 class="section-title">Mais Ofertas e Promoções</h2>
                <p class="text-muted small mb-0">Explore tudo o que está rolando na cidade</p>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php if (!empty($ultimasOfertas)): ?>
                <?php foreach ($ultimasOfertas as $oferta): ?>
                    <div class="col">
                        <div class="offer-card">
                            <div class="offer-card-media">
                                <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>">
                                    <img src="<?= $oferta->getImagemUrl() ?>" alt="<?= esc($oferta->pro_titulo) ?>" loading="lazy">
                                </a>
                                <?php if ($oferta->getPercentualDesconto() > 0): ?>
                                    <span class="offer-badge-discount">-<?= $oferta->getPercentualDesconto() ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="offer-card-body">
                                <span class="offer-partner-name">
                                    <?= esc($oferta->par_nome ?? 'Parceiro') ?>
                                </span>
                                <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="offer-card-title">
                                    <?= esc($oferta->pro_titulo) ?>
                                </a>
                                <div class="offer-price-box">
                                    <div>
                                        <div class="price-current <?= !$oferta->hasPreco() ? 'fs-6 text-muted' : '' ?>">
                                            <?= $oferta->getPrecoVendaFormatado() ?>
                                        </div>
                                    </div>
                                    <?php if (!$oferta->hasPreco()): ?>
                                        <a href="<?= $oferta->getWhatsappConsultaLink($oferta->par_whatsapp ?? '') ?>" class="btn btn-sm btn-success rounded-pill fw-bold" target="_blank">
                                            <i class="fa-brands fa-whatsapp"></i> Consultar
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn-offer-action">
                                            Ver Oferta
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
