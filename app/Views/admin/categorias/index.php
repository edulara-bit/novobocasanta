<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-folder mr-1"></i> Categorias do Portal
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/categorias/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Nova Categoria
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 50px;">Ícone</th>
                                <th>Nome da Categoria</th>
                                <th>Slug URL</th>
                                <th>Tipo</th>
                                <th style="width: 120px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td><?= $cat->cat_id ?></td>
                                    <td class="text-center"><i class="fas <?= $cat->getIconeClass() ?> text-danger"></i></td>
                                    <td><strong><?= esc($cat->cat_titulo) ?></strong></td>
                                    <td><code><?= esc($cat->cat_url) ?></code></td>
                                    <td>
                                        <?php if ((int)$cat->cat_categoria_pai === 0): ?>
                                            <span class="badge badge-primary">Principal</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Subcategoria (#<?= $cat->cat_categoria_pai ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/categorias/editar/' . $cat->cat_id) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/categorias/excluir/' . $cat->cat_id) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja excluir esta categoria?');">
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
