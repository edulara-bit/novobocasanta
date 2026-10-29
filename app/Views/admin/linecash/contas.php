<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card card-outline card-info">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-wallet text-info mr-1"></i> Contas & Carteiras LineCash
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">Total: <?= count($contas) ?> contas</span>
            </div>
        </div>
        <div class="card-body pb-2">
            <!-- Filtros por tipo de conta -->
            <div class="btn-group mb-3" role="group">
                <a href="<?= base_url('admin/linecash/contas') ?>" class="btn btn-outline-secondary <?= empty($tipo) ? 'active' : '' ?>">
                    Todas as Contas
                </a>
                <a href="<?= base_url('admin/linecash/contas?tipo=lojista') ?>" class="btn btn-outline-success <?= $tipo === 'lojista' ? 'active' : '' ?>">
                    <i class="fas fa-store mr-1"></i> Apenas Lojistas
                </a>
                <a href="<?= base_url('admin/linecash/contas?tipo=cliente') ?>" class="btn btn-outline-info <?= $tipo === 'cliente' ? 'active' : '' ?>">
                    <i class="fas fa-user mr-1"></i> Apenas Clientes (Consumidores)
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Tipo</th>
                            <th>Nome / Razão</th>
                            <th>CPF / CNPJ</th>
                            <th>WhatsApp</th>
                            <th>Saldo Disponível (LC)</th>
                            <th>Saldo Bloqueado (LC)</th>
                            <th>Última Movimentação</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contas)): ?>
                            <?php foreach ($contas as $c): ?>
                                <tr>
                                    <td>#<?= $c['lcc_id'] ?></td>
                                    <td>
                                        <?php if ($c['lcc_tipo'] === 'lojista'): ?>
                                            <span class="badge badge-success"><i class="fas fa-store"></i> Lojista</span>
                                        <?php else: ?>
                                            <span class="badge badge-info"><i class="fas fa-user"></i> Cliente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($c['lcc_nome']) ?></strong>
                                    </td>
                                    <td>
                                        <code><?= esc($c['lcc_cpf_cnpj']) ?></code>
                                    </td>
                                    <td>
                                        <?php if (!empty($c['lcc_whatsapp'])): ?>
                                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $c['lcc_whatsapp']) ?>" target="_blank" class="text-success">
                                                <i class="fab fa-whatsapp"></i> <?= esc($c['lcc_whatsapp']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-weight-bold text-success" style="font-size: 14px;">
                                        <?= number_format((float)$c['lcc_saldo'], 2, ',', '.') ?> LC
                                    </td>
                                    <td>
                                        <?php if ((float)$c['lcc_saldo_bloqueado'] > 0): ?>
                                            <span class="badge badge-warning"><?= number_format((float)$c['lcc_saldo_bloqueado'], 2, ',', '.') ?> LC</span>
                                        <?php else: ?>
                                            <span class="text-muted">0,00 LC</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= !empty($c['lcc_ultimo_movimento']) ? date('d/m/Y H:i', strtotime($c['lcc_ultimo_movimento'])) : '<span class="text-muted">Sem movimentos</span>' ?>
                                    </td>
                                    <td>
                                        <?php if ((int)$c['lcc_ativo'] === 1): ?>
                                            <span class="badge badge-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inativa</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
