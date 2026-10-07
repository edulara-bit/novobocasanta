<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-bolt text-primary mr-1"></i> Parceiros com Integração Linefast
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Sincronização em Tempo Real</span>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Como funciona a integração:</h5>
                    Os parceiros Boca Santa cadastrados e ativos no <strong>Linefast</strong> têm seus produtos próprios e estoque sincronizados automaticamente. Cada oferta exibe o selo oficial e um botão de compra que adiciona o produto instantaneamente ao carrinho do parceiro no Linefast.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Parceiro</th>
                                <th>Cidade</th>
                                <th>Linefast Partner ID</th>
                                <th>Linefast Ativo</th>
                                <th>Última Sincronização</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($parceiros)): ?>
                                <?php foreach ($parceiros as $p): ?>
                                    <tr>
                                        <td><?= $p->par_id ?></td>
                                        <td><strong><?= esc($p->getNome()) ?></strong></td>
                                        <td><?= esc($p->cid_nome ?? 'Piracicaba') ?></td>
                                        <td>
                                            <code><?= esc($p->linefast_partner_id ?: 'Não vinculado') ?></code>
                                        </td>
                                        <td>
                                            <?php if ($p->isLinefastAtivo()): ?>
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Ativo</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary px-2 py-1">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= !empty($p->linefast_sync_at) ? $p->linefast_sync_at->format('d/m/Y H:i') : '<span class="text-muted">Nunca</span>' ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/linefast/sync/' . $p->par_id) ?>" class="btn btn-primary btn-xs" title="Sincronizar Catálogo Agora">
                                                <i class="fas fa-sync-alt mr-1"></i> Sincronizar
                                            </a>
                                            <a href="<?= base_url('admin/parceiros/editar/' . $p->par_id) ?>" class="btn btn-default btn-xs" title="Editar Vínculo">
                                                <i class="fas fa-cog"></i>
                                            </a>
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
</div>
<?= $this->endSection() ?>
