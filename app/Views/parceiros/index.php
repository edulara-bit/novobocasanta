<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Guia de Parceiros em <?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?></h1>
            <p class="text-muted small mb-0">Empresas e comércios locais cadastrados no Boca Santa Ofertas</p>
        </div>
        <a href="<?= base_url('anuncie') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold px-3">
            <i class="fa-solid fa-bullhorn me-1"></i> Seja um Parceiro
        </a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mb-5">
        <?php if (!empty($parceiros)): ?>
            <?php foreach ($parceiros as $p): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="<?= $p->getLogoUrl() ?>" alt="<?= esc($p->getNome()) ?>" class="rounded-circle border" width="60" height="60" style="object-fit: cover;" onerror="this.src='<?= base_url('assets/images/logo-small.png') ?>'">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark"><?= esc($p->getNome()) ?></h5>
                                <?php if ($p->isLinefastAtivo()): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                        <i class="fa-solid fa-bolt me-1"></i> Linefast Integrado
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($p->par_descricao)): ?>
                            <p class="text-muted small flex-grow-1">
                                <?= substr(strip_tags((string)$p->par_descricao), 0, 140) ?>...
                            </p>
                        <?php endif; ?>

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <a href="<?= $p->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold">
                                Ver Perfil & Ofertas
                            </a>
                            <?php if ($p->getWhatsappLink()): ?>
                                <a href="<?= $p->getWhatsappLink() ?>" class="btn btn-sm btn-success rounded-circle" target="_blank" title="WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
