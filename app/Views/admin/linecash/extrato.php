<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-file-invoice-dollar text-primary mr-1"></i> Livro-Razão & Extrato Geral (Ledger)
            </h3>
            <div class="card-tools">
                <span class="badge badge-info">Auditoria Imutável</span>
            </div>
        </div>
        <div class="card-body pb-2">
            <!-- Filtros por tipo -->
            <div class="mb-3 d-flex flex-wrap gap-2">
                <a href="<?= base_url('admin/linecash/extrato') ?>" class="btn btn-sm btn-outline-secondary <?= empty($tipo) ? 'active' : '' ?>">
                    Todas
                </a>
                <a href="<?= base_url('admin/linecash/extrato?tipo=cashback_venda') ?>" class="btn btn-sm btn-outline-success <?= $tipo === 'cashback_venda' ? 'active' : '' ?>">
                    <i class="fas fa-gift"></i> Cashback em Vendas
                </a>
                <a href="<?= base_url('admin/linecash/extrato?tipo=pagamento_compra') ?>" class="btn btn-sm btn-outline-primary <?= $tipo === 'pagamento_compra' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Pagamento de Compras (PDV)
                </a>
                <a href="<?= base_url('admin/linecash/extrato?tipo=deposito_recarga') ?>" class="btn btn-sm btn-outline-info <?= $tipo === 'deposito_recarga' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i> Recargas de Lojistas
                </a>
                <a href="<?= base_url('admin/linecash/extrato?tipo=resgate_pix') ?>" class="btn btn-sm btn-outline-warning <?= $tipo === 'resgate_pix' ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave"></i> Resgates PIX
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Data / Hora</th>
                            <th>Operação</th>
                            <th>Origem (Pagador)</th>
                            <th>Destino (Beneficiário)</th>
                            <th>Valor (LC)</th>
                            <th>Ref. Compra</th>
                            <th>Descrição / Ledger</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transacoes)): ?>
                            <?php foreach ($transacoes as $t): ?>
                                <tr>
                                    <td>#<?= $t['lct_id'] ?></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($t['lct_criado_em'])) ?></td>
                                    <td>
                                        <?php switch ($t['lct_tipo']):
                                            case 'cashback_venda': ?>
                                                <span class="badge badge-success"><i class="fas fa-gift"></i> Cashback</span>
                                                <?php break;
                                            case 'pagamento_compra': ?>
                                                <span class="badge badge-primary"><i class="fas fa-shopping-cart"></i> Pagamento</span>
                                                <?php break;
                                            case 'deposito_recarga': ?>
                                                <span class="badge badge-info"><i class="fas fa-plus-circle"></i> Recarga</span>
                                                <?php break;
                                            case 'resgate_pix': ?>
                                                <span class="badge badge-warning"><i class="fas fa-money-bill-wave"></i> Resgate</span>
                                                <?php break;
                                            default: ?>
                                                <span class="badge badge-secondary"><?= esc($t['lct_tipo']) ?></span>
                                        <?php endswitch; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($t['origem_nome'])): ?>
                                            <strong><?= esc($t['origem_nome']) ?></strong><br>
                                            <small class="text-muted"><?= esc($t['origem_tipo']) ?>: <?= esc($t['origem_doc']) ?></small>
                                        <?php else: ?>
                                            <span class="badge badge-dark">Emissão Central BS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($t['destino_nome'])): ?>
                                            <strong><?= esc($t['destino_nome']) ?></strong><br>
                                            <small class="text-muted"><?= esc($t['destino_tipo']) ?>: <?= esc($t['destino_doc']) ?></small>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-weight-bold text-success" style="font-size: 14px;">
                                        <?= number_format((float)$t['lct_valor_lc'], 2, ',', '.') ?> LC
                                    </td>
                                    <td>
                                        <?= !empty($t['lct_valor_compra_reais']) ? 'R$ ' . number_format((float)$t['lct_valor_compra_reais'], 2, ',', '.') : '-' ?>
                                    </td>
                                    <td>
                                        <small><?= esc($t['lct_descricao'] ?: '-') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Concluída</span>
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
