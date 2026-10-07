<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-flag mr-1"></i> Estados Cadastrados
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/estados/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Novo Estado
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Nome do Estado / UF</th>
                                <th style="width: 120px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estados as $e): ?>
                                <tr>
                                    <td><?= $e['est_id'] ?></td>
                                    <td><strong><?= esc($e['est_nome']) ?></strong></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/estados/editar/' . $e['est_id']) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/estados/excluir/' . $e['est_id']) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja excluir este estado?');">
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
