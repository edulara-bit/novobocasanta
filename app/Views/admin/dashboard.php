<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- SMALL BOXES (STATISTICS) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= number_format($totalOfertas ?? 0, 0, ',', '.') ?></h3>
                <p>Ofertas & Produtos</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
            <a href="<?= base_url('admin/ofertas') ?>" class="small-box-footer">Ver todas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= number_format($totalParceiros ?? 0, 0, ',', '.') ?></h3>
                <p>Parceiros Cadastrados</p>
            </div>
            <div class="icon">
                <i class="fas fa-store"></i>
            </div>
            <a href="<?= base_url('admin/parceiros') ?>" class="small-box-footer">Gerenciar <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= number_format($totalLinefast ?? 0, 0, ',', '.') ?></h3>
                <p>Integrados no Linefast</p>
            </div>
            <div class="icon">
                <i class="fas fa-bolt"></i>
            </div>
            <a href="<?= base_url('admin/linefast') ?>" class="small-box-footer">Sincronização <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= number_format($solicitacoesLgpd ?? 0, 0, ',', '.') ?></h3>
                <p>Requisições LGPD</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <a href="<?= base_url('admin/lgpd') ?>" class="small-box-footer">Atender <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- TABELA DE ÚLTIMAS OFERTAS & INTEGRAÇÃO LINEFAST -->
<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card card-outline card-danger">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold"><i class="fas fa-fire mr-1 text-danger"></i> Últimas Ofertas Cadastradas</h3>
                <div class="card-tools">
                    <a href="<?= base_url('admin/ofertas/novo') ?>" class="btn btn-sm btn-danger"><i class="fas fa-plus mr-1"></i> Nova Oferta</a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>Produto / Oferta</th>
                            <th>Preço</th>
                            <th>Parceiro</th>
                            <th>Origem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ultimasOfertas)): ?>
                            <?php foreach ($ultimasOfertas as $o): ?>
                                <tr>
                                    <td>
                                        <img src="<?= $o->getImagemUrl() ?>" alt="Thumb" class="img-circle img-size-32 mr-2" style="object-fit: cover;">
                                        <strong><?= esc($o->pro_titulo) ?></strong>
                                    </td>
                                    <td><span class="text-success font-weight-bold"><?= $o->getPrecoVendaFormatado() ?></span></td>
                                    <td><?= esc($o->par_nome ?? 'Parceiro') ?></td>
                                    <td>
                                        <?php if ($o->isLinefast()): ?>
                                            <span class="badge badge-primary"><i class="fas fa-bolt"></i> Linefast</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Boca Santa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/ofertas/editar/' . $o->pro_id) ?>" class="btn btn-xs btn-default"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- STATUS DA INTEGRAÇÃO LINEFAST -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-bolt text-primary mr-1"></i> Status Linefast</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Integração Ativa</span>
                    <span class="badge badge-success font-weight-bold px-2 py-1">CONECTADO</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Parceiros Sincronizados</span>
                    <span class="font-weight-bold"><?= number_format($totalLinefast ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span>Link Direto ao Carrinho</span>
                    <span class="badge badge-info">Ativo (1-Clique)</span>
                </div>
                <a href="<?= base_url('admin/linefast') ?>" class="btn btn-primary btn-block font-weight-bold">
                    <i class="fas fa-sync-alt mr-1"></i> Central de Sincronização
                </a>
            </div>
        </div>

        <!-- STATUS LGPD -->
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-shield-alt text-info mr-1"></i> Conformidade LGPD</h3>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    Banner de consentimento ativo, anonimização de IPs e formulário do titular em conformidade com a Lei 13.709/2018.
                </p>
                <a href="<?= base_url('admin/lgpd') ?>" class="btn btn-outline-info btn-block btn-sm font-weight-bold">
                    Ver Requisições Pendentes
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
