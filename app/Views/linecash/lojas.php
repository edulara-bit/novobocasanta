<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- CABEÇALHO -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-white p-4 rounded-4 shadow-sm mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-store text-danger me-2"></i> Lojas Parceiras LineCash
            </h2>
            <p class="text-muted small mb-0">Confira todos os estabelecimentos credenciados onde você pode acumular créditos de cashback ou utilizar seus LineCash como desconto.</p>
        </div>
        <div>
            <a href="<?= base_url('linecash') ?>" class="btn btn-warning rounded-pill fw-bold px-4 text-dark shadow-sm">
                <i class="fa-solid fa-wallet me-1"></i> Consultar Meu Saldo
            </a>
        </div>
    </div>

    <!-- GRADE DE LOJAS -->
    <div class="row g-4">
        <?php if (empty($lojas)): ?>
            <div class="col-12">
                <div class="alert alert-light text-center py-5 rounded-4 border">
                    <i class="fa-solid fa-store-slash fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold">Nenhuma loja parceira cadastrada no momento.</h5>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($lojas as $l): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                        <?php if (!empty($l->par_imagem)): ?>
                            <img src="<?= base_url('assets/uploads/parceiros/' . $l->par_imagem) ?>" alt="<?= esc($l->getNome()) ?>" class="img-fluid rounded-3 mx-auto mb-3" style="max-height: 80px; object-fit: contain;">
                        <?php else: ?>
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3" style="height: 80px; width: 80px;">
                                <i class="fa-solid fa-store fs-2 text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?= esc($l->getNome()) ?>">
                            <?= esc($l->getNome()) ?>
                        </h6>
                        <small class="text-muted d-block mb-2"><?= esc($l->par_bairro ?: 'Piracicaba - SP') ?></small>

                        <div class="badge bg-warning bg-opacity-10 text-dark fw-bold py-2 rounded-pill mb-3">
                            <i class="fa-solid fa-coins text-warning me-1"></i> Aceita & Pontua LineCash
                        </div>

                        <div class="mt-auto d-flex flex-column gap-2">
                            <?php if (!empty($l->par_whatsapp)): ?>
                                <a href="https://api.whatsapp.com/send?phone=55<?= preg_replace('/\D/', '', $l->par_whatsapp) ?>" target="_blank" class="btn btn-success btn-sm w-100 rounded-pill fw-bold">
                                    <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp da Loja
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('piracicaba/parceiro/' . ($l->par_apelido ?: $l->par_id)) ?>" class="btn btn-outline-dark btn-sm w-100 rounded-pill">
                                Ver Catálogo / Ofertas
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
