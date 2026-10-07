<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-tags text-danger me-2"></i> Minhas Ofertas & Produtos</h3>
            <p class="text-muted small mb-0">Cadastre e gerencie as ofertas da sua empresa exibidas no portal Boca Santa.</p>
        </div>
        <a href="<?= base_url('parceiro/ofertas/criar') ?>" class="btn btn-danger rounded-pill fw-bold px-4">
            <i class="fa-solid fa-plus-circle me-1"></i> Nova Oferta
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Foto</th>
                    <th>Título</th>
                    <th>Preço "De"</th>
                    <th>Preço "Por"</th>
                    <th>Melhor Preço</th>
                    <th>Visualizações</th>
                    <th class="text-end" style="width: 140px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ofertas)): ?>
                    <?php foreach ($ofertas as $o): ?>
                        <tr>
                            <td>
                                <img src="<?= $o->getImagemUrl() ?>" alt="" class="rounded-3" width="60" height="45" style="object-fit: cover;">
                            </td>
                            <td>
                                <strong><?= esc($o->pro_titulo) ?></strong>
                                <?php if ($o->isLinefast()): ?>
                                    <span class="badge bg-primary ms-1"><i class="fa-solid fa-bolt"></i> Linefast</span>
                                <?php endif; ?>
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
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-eye me-1"></i> <?= number_format($o->pro_visitas ?? 0, 0, ',', '.') ?>
                                </span>
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-tags fs-1 d-block mb-3 text-muted"></i>
                            Você ainda não possui ofertas cadastradas.<br>
                            <a href="<?= base_url('parceiro/ofertas/criar') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold mt-2">Criar Minha Primeira Oferta</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
