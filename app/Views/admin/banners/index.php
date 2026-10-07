<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-image mr-1"></i> Banners Publicitários (Hero Carrossel Moderno)
                </h3>
                <div class="card-tools ml-auto">
                    <a href="<?= base_url('admin/banners/criar') ?>" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Novo Banner Moderno
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Banners interativos com badge, título, descrição, botão de ação, personalização de fundo (cor, degradê ou imagem) e imagem lateral de destaque.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;">Ordem</th>
                                <th>Visualização / Título</th>
                                <th>Badge de Destaque</th>
                                <th>Botão / Destino</th>
                                <th>Estilo de Fundo</th>
                                <th>Status</th>
                                <th style="width: 120px;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($banners as $b): ?>
                                <tr>
                                    <td class="text-center font-weight-bold">#<?= $b['ban_ordem'] ?? 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($b['ban_imagem_direita'])): ?>
                                                <?php $imgThumb = str_starts_with($b['ban_imagem_direita'], 'http') ? $b['ban_imagem_direita'] : base_url($b['ban_imagem_direita']); ?>
                                                <img src="<?= esc($imgThumb) ?>" alt="" style="max-height: 45px; max-width: 60px; object-fit: contain;" class="border p-1 rounded bg-light mr-2">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= esc($b['ban_titulo'] ?? 'Banner sem título') ?></strong>
                                                <?php if (!empty($b['ban_descricao'])): ?>
                                                    <br><small class="text-muted text-truncate d-inline-block" style="max-width: 300px;"><?= esc($b['ban_descricao']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning text-dark font-weight-bold"><?= esc($b['ban_badge'] ?? '⭐ DESTAQUE') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border"><?= esc($b['ban_botao_texto'] ?? 'Ver Mais') ?></span>
                                        <br><small class="text-muted"><code>/<?= esc($b['ban_botao_link'] ?? '') ?></code></small>
                                    </td>
                                    <td>
                                        <?php if (($b['ban_tipo_fundo'] ?? '') === 'imagem' && !empty($b['ban_fundo_imagem'])): ?>
                                            <span class="badge badge-info">Imagem de Fundo</span>
                                        <?php elseif (($b['ban_tipo_fundo'] ?? '') === 'cor'): ?>
                                            <span class="badge" style="background: <?= esc($b['ban_fundo_cor'] ?? '#dc2626') ?>; color: #fff;">Cor Única</span>
                                        <?php else: ?>
                                            <span class="badge badge-primary">Degradê</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($b['ban_status'] ?? 'ativo') === 'ativo'): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/banners/editar/' . $b['ban_id']) ?>" class="btn btn-info btn-xs mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/banners/excluir/' . $b['ban_id']) ?>" class="btn btn-danger btn-xs" title="Excluir" onclick="return confirm('Deseja excluir este banner?');">
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
