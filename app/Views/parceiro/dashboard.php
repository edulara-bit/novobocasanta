<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<!-- BANNER DO PARCEIRO -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <div class="row align-items-center g-4">
        <div class="col-auto">
            <img src="<?= $parceiro->getLogoUrl() ?>" alt="<?= esc($parceiro->getNome()) ?>" style="max-height: 90px; max-width: 140px; object-fit: contain;" class="border p-2 rounded-3 bg-light" onerror="this.src='<?= base_url('assets/images/logo.png') ?>'">
        </div>
        <div class="col">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h2 class="fw-bold text-dark mb-0"><?= esc($parceiro->getNome()) ?></h2>
                <?php if ($parceiro->isLinefastAtivo()): ?>
                    <span class="badge bg-primary px-3 py-1 rounded-pill"><i class="fa-solid fa-bolt me-1"></i> Linefast Integrado</span>
                <?php endif; ?>
            </div>
            <p class="text-muted small mb-3">
                <i class="fa-solid fa-location-dot text-danger me-1"></i> <?= esc($parceiro->par_endereco ? $parceiro->par_endereco . ', ' . $parceiro->par_numero : 'Piracicaba - SP') ?> &bull; 
                <i class="fa-solid fa-envelope me-1"></i> <?= esc($parceiro->par_email ?? '') ?>
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('parceiro/fidelidade/lancamentos') ?>" class="btn btn-warning btn-sm rounded-pill fw-bold px-3 text-dark shadow-sm">
                    <i class="fa-solid fa-stamp me-1"></i> Lançar Pontos / Compras
                </a>
                <a href="<?= base_url('parceiro/ofertas/criar') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold px-3">
                    <i class="fa-solid fa-plus-circle me-1"></i> Criar Nova Oferta
                </a>
                <a href="<?= base_url('parceiro/fidelidade/criar') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-id-card me-1"></i> Novo Cartão
                </a>
                <a href="<?= base_url('parceiro/perfil') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-edit me-1"></i> Editar Meus Dados
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MÉTRICAS RÁPIDAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
            <div class="p-3 bg-danger-subtle text-danger rounded-4 fs-3">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Ofertas Cadastradas</span>
                <h3 class="fw-bold mb-0 text-dark"><?= count($ofertas) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
            <div class="p-3 bg-primary-subtle text-primary rounded-4 fs-3">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Visualizações Totais</span>
                <h3 class="fw-bold mb-0 text-dark"><?= number_format((int)($parceiro->par_acesso ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white d-flex flex-row align-items-center gap-3">
            <div class="p-3 bg-success-subtle text-success rounded-4 fs-3">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Cartões Fidelidade</span>
                <h3 class="fw-bold mb-0 text-dark"><?= count($cartoes) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- ÚLTIMAS OFERTAS -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-tags text-danger me-2"></i> Minhas Ofertas Mais Recentes</h5>
        <a href="<?= base_url('parceiro/ofertas') ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">Ver Todas</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Foto</th>
                    <th>Título da Oferta</th>
                    <th>Preço "De"</th>
                    <th>Preço "Por"</th>
                    <th>Melhor Preço</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ofertas)): ?>
                    <?php foreach (array_slice($ofertas, 0, 5) as $o): ?>
                        <tr>
                            <td>
                                <img src="<?= $o->getImagemUrl() ?>" alt="" class="rounded-3" width="55" height="40" style="object-fit: cover;">
                            </td>
                            <td>
                                <strong><?= esc($o->pro_titulo) ?></strong>
                            </td>
                            <td class="text-muted small text-decoration-line-through">
                                <?= $o->hasPrecoDe() ? $o->getPrecoOriginalFormatado() : '-' ?>
                            </td>
                            <td class="text-danger fw-bold">
                                <?= $o->getPrecoVendaFormatado() ?>
                            </td>
                            <td>
                                <?php if (!empty($o->pro_melhor_preco) && (int)$o->pro_melhor_preco === 1): ?>
                                    <span class="badge bg-success-subtle text-success border">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Não</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('parceiro/ofertas/editar/' . $o->pro_id) ?>" class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="<?= base_url('parceiro/ofertas/excluir/' . $o->pro_id) ?>" class="btn btn-sm btn-outline-danger rounded-pill" title="Excluir" onclick="return confirm('Deseja excluir esta oferta?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Nenhuma oferta cadastrada no momento. <a href="<?= base_url('parceiro/ofertas/criar') ?>" class="text-danger fw-bold">Cadastrar a primeira oferta</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
