<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-users-cog mr-1"></i> Usuários Administradores
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/administradores/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-user-plus mr-1"></i> Novo Administrador
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Nome</th>
                                <th>Login / Usuário</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th style="width: 120px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($administradores as $adm): ?>
                                <tr>
                                    <td><?= $adm['adm_id'] ?></td>
                                    <td><strong><?= esc($adm['adm_nome'] ?? $adm['adm_login']) ?></strong></td>
                                    <td><code><?= esc($adm['adm_login']) ?></code></td>
                                    <td><?= esc($adm['adm_email'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-success"><?= esc($adm['adm_status'] ?? 'ativo') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/administradores/editar/' . $adm['adm_id']) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ((int)$adm['adm_id'] !== (int)session()->get('admin_id')): ?>
                                            <a href="<?= base_url('admin/administradores/excluir/' . $adm['adm_id']) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja realmente excluir este administrador?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php endif; ?>
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
