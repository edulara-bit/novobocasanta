<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-store mr-1"></i> Parceiros Comerciais & Anunciantes
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/parceiros/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Novo Parceiro
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th style="width: 70px;">Logo</th>
                                <th>Nome Fantasia / Empresa</th>
                                <th>Cidade</th>
                                <th>Telefone / Whats</th>
                                <th>Acessos</th>
                                <th>Status</th>
                                <th style="width: 220px;" class="text-center">Ações de Gestão</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parceiros as $p): ?>
                                <tr>
                                    <td><?= $p->par_id ?></td>
                                    <td class="text-center">
                                        <img src="<?= $p->getLogoUrl() ?>" alt="" style="max-height: 40px; max-width: 60px; object-fit: contain;">
                                    </td>
                                    <td>
                                        <strong><?= esc($p->getNome()) ?></strong>
                                        <?php if ($p->isLinefastAtivo()): ?>
                                            <span class="badge badge-linefast ml-1"><i class="fas fa-bolt"></i> Linefast</span>
                                        <?php endif; ?>
                                        <br><small class="text-muted"><?= esc($p->par_email ?? '') ?></small>
                                    </td>
                                    <td><?= esc($p->cid_nome ?? 'Piracicaba') ?></td>
                                    <td><?= esc($p->par_whatsapp ?: $p->par_telefone ?: '-') ?></td>
                                    <td><span class="badge badge-secondary"><?= (int)($p->par_acesso ?? 0) ?> views</span></td>
                                    <td>
                                        <span class="badge badge-success">Ativo</span>
                                    </td>
                                    <td class="text-center">
                                        <!-- Ver Produtos -->
                                        <a href="<?= base_url('admin/ofertas?parceiro=' . $p->par_id) ?>" class="btn btn-primary btn-xs mr-1" title="Ver Ofertas deste Parceiro">
                                            <i class="fas fa-box-open"></i>
                                        </a>

                                        <!-- Logar como Parceiro (Item 10) -->
                                        <a href="<?= base_url('admin/parceiros/login-as/' . $p->par_id) ?>" class="btn btn-warning btn-xs mr-1 font-weight-bold" title="Logar como Parceiro (Painel Restrito)" target="_blank">
                                            <i class="fas fa-sign-in-alt"></i> Logar
                                        </a>

                                        <!-- Zerar Acessos (Item 10) -->
                                        <a href="<?= base_url('admin/parceiros/zerar-acessos/' . $p->par_id) ?>" class="btn btn-secondary btn-xs mr-1" title="Zerar Contador de Acessos" onclick="return confirm('Deseja realmente zerar os contadores de acessos e visualizações deste parceiro?');">
                                            <i class="fas fa-undo"></i>
                                        </a>

                                        <!-- Editar -->
                                        <a href="<?= base_url('admin/parceiros/editar/' . $p->par_id) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Excluir -->
                                        <a href="<?= base_url('admin/parceiros/excluir/' . $p->par_id) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja realmente excluir este parceiro?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
