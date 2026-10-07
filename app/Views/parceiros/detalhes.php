<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="text-decoration-none text-muted">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/parceiros') ?>" class="text-decoration-none text-muted">Parceiros</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($parceiro->getNome()) ?></li>
        </ol>
    </nav>

    <!-- HEADER DO PARCEIRO COM DADOS COMPLETOS -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-white">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <img src="<?= $parceiro->getLogoUrl() ?>" alt="<?= esc($parceiro->getNome()) ?>" style="max-height: 130px; max-width: 250px; width: auto; height: auto; object-fit: contain;" onerror="this.src='<?= base_url('assets/images/logo.png') ?>'">
            </div>
            <div class="col">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h1 class="h2 fw-bold text-dark mb-0"><?= esc($parceiro->getNome()) ?></h1>
                    <?php if ($parceiro->isLinefastAtivo()): ?>
                        <span class="badge bg-primary px-3 py-1 rounded-pill"><i class="fa-solid fa-bolt me-1"></i> Linefast Integrado</span>
                    <?php endif; ?>
                </div>

                <p class="text-muted mb-3">
                    <i class="fa-solid fa-location-dot text-danger me-1"></i> <?= esc($parceiro->getEnderecoCompleto($cidadeAtual['cid_nome'] ?? 'Piracicaba')) ?>
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <?php if ($parceiro->getWhatsappLink()): ?>
                        <a href="<?= $parceiro->getWhatsappLink() ?>" class="btn btn-success btn-sm rounded-pill fw-bold px-3" target="_blank">
                            <i class="fa-brands fa-whatsapp me-1 fs-6"></i> WhatsApp
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($parceiro->par_email)): ?>
                        <a href="mailto:<?= esc($parceiro->par_email) ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="fa-solid fa-envelope me-1"></i> <?= esc($parceiro->par_email) ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($parceiro->par_site)): ?>
                        <a href="<?= esc($parceiro->par_site) ?>" class="btn btn-outline-secondary btn-sm rounded-pill" target="_blank">
                            <i class="fa-solid fa-globe me-1"></i> Website Oficial
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($parceiro->par_descricao)): ?>
            <hr class="my-4">
            <div class="text-secondary lh-lg small">
                <strong class="text-dark d-block mb-1">Sobre a Empresa:</strong>
                <?= nl2br(esc($parceiro->par_descricao)) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- MAPA DO GOOGLE MAPS DO PARCEIRO (ITEM 1) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 bg-white">
        <h4 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-map-location-dot text-danger me-2"></i> Localização do Estabelecimento</h4>
        <div class="ratio ratio-21x9 rounded-4 overflow-hidden border shadow-sm">
            <iframe 
                src="<?= $parceiro->getGoogleMapsEmbedUrl($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?>" 
                width="100%" 
                height="320" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>

    <!-- OFERTAS DO PARCEIRO -->
    <div class="mb-5">
        <h3 class="section-title mb-4">Ofertas & Produtos de <?= esc($parceiro->getNome()) ?> (<?= count($ofertas) ?>)</h3>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php if (!empty($ofertas)): ?>
                <?php foreach ($ofertas as $oferta): ?>
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
                                <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="offer-card-title">
                                    <?= esc($oferta->pro_titulo) ?>
                                </a>
                                <div class="offer-price-box">
                                    <div class="price-current <?= !$oferta->hasPreco() ? 'fs-6 text-muted' : '' ?>">
                                        <?= $oferta->getPrecoVendaFormatado() ?>
                                    </div>
                                    <?php if ($oferta->isLinefast()): ?>
                                        <a href="<?= $oferta->getLinefastCartUrl() ?>" class="btn-linefast-direct" target="_blank">
                                            <i class="fa-solid fa-cart-shopping"></i> Comprar
                                        </a>
                                    <?php elseif (!$oferta->hasPreco()): ?>
                                        <a href="<?= $oferta->getWhatsappConsultaLink($parceiro->par_whatsapp ?? '') ?>" class="btn btn-sm btn-success rounded-pill fw-bold" target="_blank">
                                            <i class="fa-brands fa-whatsapp"></i> Consultar
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $oferta->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn-offer-action">
                                            Ver
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Nenhuma oferta ativa cadastrada para este parceiro no momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
