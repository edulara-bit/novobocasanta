<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-id-card text-danger me-2"></i> Meus Cartões Fidelidade</h3>
            <p class="text-muted small mb-0">Crie programas de fidelidade, pontuação e vantagens exclusivas para seus clientes.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('parceiro/fidelidade/lancamentos') ?>" class="btn btn-warning rounded-pill fw-bold px-4 text-dark shadow-sm">
                <i class="fa-solid fa-stamp me-1"></i> Lançar Pontos / Compras
            </a>
            <a href="<?= base_url('parceiro/fidelidade/criar') ?>" class="btn btn-danger rounded-pill fw-bold px-4 shadow-sm">
                <i class="fa-solid fa-plus-circle me-1"></i> Novo Cartão Fidelidade
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Campanha / Nome do Cartão</th>
                    <th>Oferta Vinculada</th>
                    <th>Meta de Pontos</th>
                    <th>Validade</th>
                    <th>Regulamento / Recompensa</th>
                    <th class="text-end" style="width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cartoes)): ?>
                    <?php 
                        $ofertasMap = [];
                        if (!empty($ofertas)) {
                            foreach ($ofertas as $of) {
                                $ofertasMap[$of->pro_id] = $of->pro_titulo;
                            }
                        }
                    ?>
                    <?php foreach ($cartoes as $c): ?>
                        <tr>
                            <td>#<?= $c['car_id'] ?></td>
                            <td>
                                <strong><?= esc($c['car_nome']) ?></strong>
                            </td>
                            <td>
                                <?php if (!empty($c['car_oferta']) && isset($ofertasMap[$c['car_oferta']])): ?>
                                    <span class="badge bg-primary-subtle text-primary border">
                                        <i class="fa-solid fa-tag me-1"></i> <?= esc($ofertasMap[$c['car_oferta']]) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="fa-solid fa-store me-1"></i> Todos os Produtos
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border px-3 py-1 rounded-pill">
                                    <i class="fa-solid fa-star me-1"></i> <?= $c['car_pontos'] ?> Pontos
                                </span>
                            </td>
                            <td><?= $c['car_validade'] ?> dias</td>
                            <td><small class="text-muted text-truncate d-inline-block" style="max-width: 250px;"><?= esc($c['car_regras'] ?? 'Recompensa ao completar') ?></small></td>
                            <td class="text-end">
                                <a href="<?= base_url('parceiro/fidelidade/editar/' . $c['car_id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="<?= base_url('parceiro/fidelidade/excluir/' . $c['car_id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill" title="Excluir" onclick="return confirm('Deseja excluir este cartão fidelidade?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-id-card fs-1 d-block mb-3 text-muted"></i>
                            Nenhum cartão fidelidade ativo.<br>
                            <a href="<?= base_url('parceiro/fidelidade/criar') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold mt-2">Criar Campanha de Fidelidade</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
