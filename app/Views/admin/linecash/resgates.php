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

    <div class="card card-outline card-danger">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-money-bill-wave text-danger mr-1"></i> Fila de Resgates PIX (Lojistas)
            </h3>
            <div class="card-tools">
                <span class="badge badge-warning">Prazo de Pagamento: até 24h</span>
            </div>
        </div>
        <div class="card-body pb-2">
            <div class="alert alert-warning py-2">
                <strong><i class="fas fa-shield-alt"></i> Regras de Resgate Financeiro:</strong>
                O lojista solicita resgate de no mínimo 100 LC. O sistema retém automaticamente <strong>2% de deságio</strong> em favor do Boca Santa e libera o valor líquido para pagamento via PIX. Enquanto pendente, o saldo fica bloqueado para evitar uso duplo.
            </div>

            <!-- Filtros -->
            <div class="btn-group mb-3" role="group">
                <a href="<?= base_url('admin/linecash/resgates') ?>" class="btn btn-outline-secondary <?= empty($status) ? 'active' : '' ?>">
                    Todos
                </a>
                <a href="<?= base_url('admin/linecash/resgates?status=pendente') ?>" class="btn btn-outline-warning <?= $status === 'pendente' ? 'active' : '' ?>">
                    <i class="fas fa-clock mr-1"></i> Pendentes
                </a>
                <a href="<?= base_url('admin/linecash/resgates?status=pago') ?>" class="btn btn-outline-success <?= $status === 'pago' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle mr-1"></i> Pagos
                </a>
                <a href="<?= base_url('admin/linecash/resgates?status=cancelado') ?>" class="btn btn-outline-danger <?= $status === 'cancelado' ? 'active' : '' ?>">
                    <i class="fas fa-times-circle mr-1"></i> Cancelados
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Data Solicitação</th>
                            <th>Lojista</th>
                            <th>Saldo Solicitado (LC)</th>
                            <th>Deságio BS (2%)</th>
                            <th>Líquido a Transferir (R$)</th>
                            <th>Chave PIX</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($resgates)): ?>
                            <?php foreach ($resgates as $res): ?>
                                <tr>
                                    <td>#<?= $res['lcr_id'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($res['lcr_criado_em'])) ?></td>
                                    <td>
                                        <strong><?= esc($res['lcc_nome']) ?></strong><br>
                                        <small class="text-muted"><?= esc($res['lcc_cpf_cnpj']) ?></small>
                                    </td>
                                    <td class="font-weight-bold">
                                        <?= number_format((float)$res['lcr_valor_lc'], 2, ',', '.') ?> LC
                                    </td>
                                    <td class="text-danger font-weight-bold">
                                        R$ <?= number_format((float)$res['lcr_desagio_reais'], 2, ',', '.') ?>
                                    </td>
                                    <td class="text-success font-weight-bold" style="font-size: 15px;">
                                        R$ <?= number_format((float)$res['lcr_valor_liquido_reais'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm" style="max-width: 250px;">
                                            <input type="text" class="form-control" value="<?= esc($res['lcr_chave_pix']) ?>" readonly id="pix_<?= $res['lcr_id'] ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('<?= esc($res['lcr_chave_pix']) ?>'); alert('Chave PIX copiada!');">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if (!empty($res['lcr_tipo_chave'])): ?>
                                            <small class="text-muted">Tipo: <?= esc($res['lcr_tipo_chave']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($res['lcr_status'] === 'pendente'): ?>
                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-hourglass-half"></i> Aguardando PIX</span>
                                        <?php elseif ($res['lcr_status'] === 'pago'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Pago</span>
                                            <?php if (!empty($res['lcr_pago_em'])): ?>
                                                <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($res['lcr_pago_em'])) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-times"></i> Cancelado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($res['lcr_status'] === 'pendente'): ?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalPagar<?= $res['lcr_id'] ?>" title="Marcar como Pago">
                                                    <i class="fas fa-check-circle"></i> Pagar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modalCancelar<?= $res['lcr_id'] ?>" title="Cancelar">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </div>

                                            <!-- Modal Confirmar Pagamento -->
                                            <div class="modal fade" id="modalPagar<?= $res['lcr_id'] ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST" action="<?= base_url('admin/linecash/resgates/pagar/' . $res['lcr_id']) ?>">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Baixa de Resgate PIX #<?= $res['lcr_id'] ?></h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Lojista: <strong><?= esc($res['lcc_nome']) ?></strong></p>
                                                                <p>Chave PIX: <code><?= esc($res['lcr_chave_pix']) ?></code></p>
                                                                <p class="fs-5">Valor a Pagar: <strong class="text-success">R$ <?= number_format((float)$res['lcr_valor_liquido_reais'], 2, ',', '.') ?></strong></p>
                                                                <div class="form-group">
                                                                    <label>URL / Link do Comprovante PIX (Opcional):</label>
                                                                    <input type="text" name="comprovante_pix" class="form-control" placeholder="Link do comprovante ou número da transação">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Observação Interna:</label>
                                                                    <input type="text" name="observacao" class="form-control" placeholder="Ex: Transferido via Nubank">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                                <button type="submit" class="btn btn-success">Confirmar Baixa e Pagamento</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Cancelar Resgate -->
                                            <div class="modal fade" id="modalCancelar<?= $res['lcr_id'] ?>" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST" action="<?= base_url('admin/linecash/resgates/cancelar/' . $res['lcr_id']) ?>">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title font-weight-bold">Cancelar Pedido de Resgate #<?= $res['lcr_id'] ?></h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Ao cancelar, o saldo de <strong><?= number_format((float)$res['lcr_valor_lc'], 2, ',', '.') ?> LC</strong> será desbloqueado e devolvido à carteira do lojista.</p>
                                                                <div class="form-group">
                                                                    <label>Motivo do Cancelamento:</label>
                                                                    <textarea name="motivo" class="form-control" rows="3" required placeholder="Ex: Chave PIX incorreta / Lojista solicitou estorno"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?php if (!empty($res['lcr_comprovante_pix'])): ?>
                                                <a href="<?= esc($res['lcr_comprovante_pix']) ?>" target="_blank" class="btn btn-xs btn-outline-info">
                                                    <i class="fas fa-file-invoice"></i> Comprovante
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fas fa-lock"></i> Finalizado</span>
                                            <?php endif; ?>
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
<?= $this->endSection() ?>
