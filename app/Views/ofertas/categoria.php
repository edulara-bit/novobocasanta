<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="text-decoration-none text-muted">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($categoria->cat_titulo ?? 'Categoria') ?></li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1"><?= esc($categoria->cat_titulo ?? 'Ofertas') ?> em <?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?></h1>
            <p class="text-muted small mb-0"><?= count($ofertas) ?> ofertas encontradas nesta categoria</p>
        </div>

        <!-- FILTROS & ORDENAÇÃO -->
        <div class="d-flex align-items-center gap-2">
            <label class="small text-muted fw-semibold">Ordenar:</label>
            <select class="form-select form-select-sm rounded-pill" onchange="location = this.value;">
                <option value="?ordem=relevancia" <?= ($ordemAtual ?? '') === 'relevancia' ? 'selected' : '' ?>>Relevância</option>
                <option value="?ordem=menor_preco" <?= ($ordemAtual ?? '') === 'menor_preco' ? 'selected' : '' ?>>Menor Preço</option>
                <option value="?ordem=maior_preco" <?= ($ordemAtual ?? '') === 'maior_preco' ? 'selected' : '' ?>>Maior Preço</option>
                <option value="?ordem=mais_recentes" <?= ($ordemAtual ?? '') === 'mais_recentes' ? 'selected' : '' ?>>Mais Recentes</option>
            </select>
        </div>
    </div>

    <!-- LISTA DE OFERTAS -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-5">
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
                            <span class="offer-partner-name">
                                <i class="fa-solid fa-store text-danger"></i> <?= esc($oferta->par_nome ?? 'Parceiro') ?>
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
                                    <a href="<?= $oferta->getLinefastCartUrl() ?>" class="btn-linefast-direct" target="_blank">
                                        <i class="fa-solid fa-cart-shopping"></i> Comprar
                                    </a>
                                <?php elseif (!$oferta->hasPreco()): ?>
                                    <a href="<?= $oferta->getWhatsappConsultaLink($oferta->par_whatsapp ?? '') ?>" class="btn btn-sm btn-success rounded-pill fw-bold" target="_blank">
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
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-tags text-muted display-4 mb-3"></i>
                <h4 class="text-dark fw-bold">Nenhuma oferta ativa encontrada</h4>
                <p class="text-muted">Não encontramos ofertas nesta categoria para <?= esc($cidadeAtual['cid_nome'] ?? 'esta cidade') ?> no momento.</p>
                <a href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn btn-danger rounded-pill px-4 fw-semibold mt-2">
                    Voltar para a Página Inicial
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
