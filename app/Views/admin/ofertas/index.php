<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-tags mr-1"></i> Lista de Ofertas & Produtos Cadastrados
                    <?php if (!empty($parceiroFiltro)): ?>
                        <span class="badge badge-info ml-2">Filtrando por: <?= esc($parceiroFiltro->getNome()) ?></span>
                        <a href="<?= base_url('admin/ofertas') ?>" class="btn btn-xs btn-outline-secondary ml-1"><i class="fas fa-times"></i> Limpar</a>
                    <?php endif; ?>
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/ofertas/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Nova Oferta
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th style="width: 70px;">Foto</th>
                                <th>Título da Oferta</th>
                                <th>Parceiro</th>
                                <th>Preço Original</th>
                                <th>Preço Promo</th>
                                <th>Status</th>
                                <th style="width: 140px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ofertas as $o): ?>
                                <tr>
                                    <td><?= $o->pro_id ?></td>
                                    <td class="text-center">
                                        <img src="<?= $o->getImagemUrl() ?>" alt="" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <strong><?= esc($o->pro_titulo) ?></strong>
                                        <?php if (!empty($o->pro_melhor_preco) && (int)$o->pro_melhor_preco === 1): ?>
                                            <span class="badge badge-success ml-1">Melhor Preço</span>
                                        <?php endif; ?>
                                        <?php if ($o->isLinefast()): ?>
                                            <span class="badge badge-linefast ml-1"><i class="fas fa-bolt"></i> Linefast</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($o->par_nome ?? 'Parceiro #' . $o->pro_parceiro) ?></td>
                                    <td><?= $o->hasPrecoDe() ? $o->getPrecoOriginalFormatado() : '-' ?></td>
                                    <td class="font-weight-bold text-danger"><?= $o->getPrecoVendaFormatado() ?></td>
                                    <td>
                                        <span class="badge badge-success">Ativa</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/ofertas/editar/' . $o->pro_id) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/ofertas/excluir/' . $o->pro_id) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja realmente excluir esta oferta?');">
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
