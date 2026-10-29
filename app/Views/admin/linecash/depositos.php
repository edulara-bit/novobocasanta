<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- CABEÇALHO & FILTROS -->
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-coins text-warning mr-1"></i> Recargas & Depósitos de Lojistas
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalRecargaManual">
                    <i class="fas fa-plus-circle mr-1"></i> Lançar Recarga Manual
                </button>
            </div>
        </div>
        <div class="card-body pb-2">
            <div class="alert alert-info py-2">
                <strong><i class="fas fa-info-circle"></i> Regra Operacional:</strong>
                O lojista transfere o valor via PIX para o Nubank do Boca Santa. Ao aprovar, o sistema credita <strong>90% do valor em LC</strong> na conta do lojista e retém <strong>10% como comissão</strong> permanente para o Boca Santa.
            </div>

            <!-- Filtros de Status -->
            <div class="btn-group mb-3" role="group">
                <a href="<?= base_url('admin/linecash/depositos') ?>" class="btn btn-outline-secondary <?= empty($status) ? 'active' : '' ?>">
                    Todos
                </a>
                <a href="<?= base_url('admin/linecash/depositos?status=pendente') ?>" class="btn btn-outline-warning <?= $status === 'pendente' ? 'active' : '' ?>">
                    <i class="fas fa-clock mr-1"></i> Pendentes
                </a>
                <a href="<?= base_url('admin/linecash/depositos?status=aprovado') ?>" class="btn btn-outline-success <?= $status === 'aprovado' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle mr-1"></i> Aprovados
                </a>
                <a href="<?= base_url('admin/linecash/depositos?status=rejeitado') ?>" class="btn btn-outline-danger <?= $status === 'rejeitado' ? 'active' : '' ?>">
                    <i class="fas fa-times-circle mr-1"></i> Rejeitados
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Data</th>
                            <th>Lojista</th>
                            <th>Valor Transferido (R$)</th>
                            <th>Crédito LC (90%)</th>
                            <th>Comissão BS (10%)</th>
                            <th>Status</th>
                            <th>Comprovante / Obs</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($depositos)): ?>
                            <?php foreach ($depositos as $dep): ?>
                                <tr>
                                    <td>#<?= $dep['lcd_id'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($dep['lcd_criado_em'])) ?></td>
                                    <td>
                                        <strong><?= esc($dep['lcc_nome']) ?></strong><br>
                                        <small class="text-muted"><i class="fab fa-whatsapp text-success"></i> <?= esc($dep['lcc_whatsapp'] ?: 'Sem whatsapp') ?></small>
                                    </td>
                                    <td class="font-weight-bold">
                                        R$ <?= number_format((float)$dep['lcd_valor_reais'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-success font-weight-bold">
                                        <?= number_format((float)$dep['lcd_valor_lc_creditado'], 2, ',', '.') ?> LC
                                    </td>
                                    <td class="text-info font-weight-bold">
                                        R$ <?= number_format((float)$dep['lcd_taxa_bs_reais'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($dep['lcd_status'] === 'pendente'): ?>
                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-hourglass-half"></i> Pendente</span>
                                        <?php elseif ($dep['lcd_status'] === 'aprovado'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Aprovado</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-times"></i> Rejeitado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($dep['lcd_comprovante_url'])): ?>
                                            <a href="<?= esc($dep['lcd_comprovante_url']) ?>" target="_blank" class="btn btn-xs btn-outline-info">
                                                <i class="fas fa-file-invoice"></i> Ver Comprovante
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($dep['lcd_observacao'])): ?>
                                            <small class="d-block text-muted mt-1"><?= esc($dep['lcd_observacao']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($dep['lcd_status'] === 'pendente'): ?>
                                            <div class="btn-group">
                                                <a href="<?= base_url('admin/linecash/depositos/aprovar/' . $dep['lcd_id']) ?>" 
                                                   onclick="return confirm('Confirmar o crédito de <?= number_format((float)$dep['lcd_valor_lc_creditado'], 2, ',', '.') ?> LC para <?= esc($dep['lcc_nome']) ?>?');"
                                                   class="btn btn-sm btn-success" title="Aprovar e Liberar LC">
                                                    <i class="fas fa-check"></i> Aprovar
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalRejeitar<?= $dep['lcd_id'] ?>" title="Rejeitar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>

                                            <!-- Modal de Rejeição -->
                                            <div class="modal fade" id="modalRejeitar<?= $dep['lcd_id'] ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST" action="<?= base_url('admin/linecash/depositos/rejeitar/' . $dep['lcd_id']) ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title font-weight-bold">Rejeitar Depósito #<?= $dep['lcd_id'] ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Lojista: <strong><?= esc($dep['lcc_nome']) ?></strong></p>
                                                                <p>Valor: <strong>R$ <?= number_format((float)$dep['lcd_valor_reais'], 2, ',', '.') ?></strong></p>
                                                                <div class="form-group">
                                                                    <label>Motivo da Rejeição:</label>
                                                                    <textarea name="motivo" class="form-control" rows="3" required placeholder="Ex: Comprovante ilegível ou valor não caiu na conta."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                                <button type="submit" class="btn btn-danger">Confirmar Rejeição</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-lock"></i> Finalizado</span>
                                        <?php endif; ?>
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

<!-- MODAL LANÇAR RECARGA MANUAL -->
<div class="modal fade" id="modalRecargaManual" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?= base_url('admin/linecash/depositos/manual') ?>">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-1"></i> Lançar Recarga para Lojista</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Selecione o Parceiro / Lojista:</label>
                        <select name="parceiro_id" class="form-control" required style="width: 100%;">
                            <option value="">Selecione um parceiro...</option>
                            <?php foreach ($parceiros as $p): ?>
                                <option value="<?= $p->par_id ?>">
                                    <?= esc($p->getNome()) ?> (ID: <?= $p->par_id ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Valor em Reais (Mínimo R$ 100,00):</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                            <input type="text" name="valor" class="form-control" placeholder="100,00" required>
                        </div>
                        <small class="form-text text-muted">90% será creditado em LC e 10% retido como comissão.</small>
                    </div>

                    <div class="form-group">
                        <label>Observação / Referência:</label>
                        <input type="text" name="observacao" class="form-control" placeholder="Ex: PIX recebido Nubank">
                    </div>

                    <div class="form-check mt-3">
                        <input type="checkbox" name="aprovar_imediato" value="1" class="form-check-input" id="checkAprovar" checked>
                        <label class="form-check-label font-weight-bold text-success" for="checkAprovar">
                            Aprovar e creditar o saldo LC imediatamente
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Lançamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
