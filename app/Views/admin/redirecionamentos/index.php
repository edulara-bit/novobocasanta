<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-directions mr-1"></i> Redirecionamentos de URLs & SEO (301)
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/redirecionamentos/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Novo Redirecionamento
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Gerencie redirecionamentos permanentes (301) para preservar a autoridade de busca do Google e evitar erros 404 de links legados.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Título / Descrição</th>
                                <th>URL Antiga (Origem)</th>
                                <th>URL Nova (Destino)</th>
                                <th style="width: 120px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($redirecionamentos as $r): ?>
                                <tr>
                                    <td><?= $r['red_id'] ?></td>
                                    <td><strong><?= esc($r['red_titulo'] ?? '-') ?></strong></td>
                                    <td><code>/<?= esc($r['red_link_antigo']) ?></code></td>
                                    <td><span class="text-success font-weight-bold">/<?= esc($r['red_link_novo']) ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/redirecionamentos/editar/' . $r['red_id']) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/redirecionamentos/excluir/' . $r['red_id']) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja excluir este redirecionamento?');">
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
