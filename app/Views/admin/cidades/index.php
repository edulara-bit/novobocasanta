<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-map-marker-alt mr-1"></i> Cidades Atendidas pelo Portal
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/cidades/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Nova Cidade
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Nome da Cidade</th>
                                <th>Estado</th>
                                <th>URL Slug</th>
                                <th style="width: 120px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cidades as $c): ?>
                                <tr>
                                    <td><?= $c['cid_id'] ?></td>
                                    <td><strong><?= esc($c['cid_nome']) ?></strong></td>
                                    <td><?= esc($c['est_nome'] ?? 'SP') ?></td>
                                    <td><code><?= esc($c['cid_url']) ?></code></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/cidades/editar/' . $c['cid_id']) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/cidades/excluir/' . $c['cid_id']) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja excluir esta cidade?');">
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
