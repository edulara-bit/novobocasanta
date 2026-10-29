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

    <!-- CARDS DE MÉTRICAS GERAIS -->
    <div class="row">
        <!-- Saldo em Circulação -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3><?= number_format($metricas['saldo_total_circulacao'], 2, ',', '.') ?> <small style="color: #fff; font-size: 16px;">LC</small></h3>
                    <p>Total em Circulação</p>
                    <div style="font-size: 11px; opacity: 0.9;">
                        Clientes: <strong><?= number_format($metricas['saldo_clientes'], 2, ',', '.') ?> LC</strong><br>
                        Lojistas: <strong><?= number_format($metricas['saldo_lojistas'], 2, ',', '.') ?> LC</strong>
                    </div>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <a href="<?= base_url('admin/linecash/contas') ?>" class="small-box-footer">
                    Ver Contas & Carteiras <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Receita Boca Santa -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>R$ <?= number_format($metricas['receita_total_bs'], 2, ',', '.') ?></h3>
                    <p>Receita Retida (Boca Santa)</p>
                    <div style="font-size: 11px; opacity: 0.9;">
                        10% Recargas: <strong>R$ <?= number_format($metricas['receita_comissoes_recargas'], 2, ',', '.') ?></strong><br>
                        2% Deságio Saques: <strong>R$ <?= number_format($metricas['receita_desagio_resgates'], 2, ',', '.') ?></strong>
                    </div>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <a href="<?= base_url('admin/linecash/extrato') ?>" class="small-box-footer">
                    Ver Extrato Consolidado <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Recargas Pendentes -->
        <div class="col-lg-3 col-6">
            <div class="small-box <?= $metricas['depositos_pendentes_count'] > 0 ? 'bg-gradient-warning' : 'bg-gradient-secondary' ?>">
                <div class="inner">
                    <h3><?= $metricas['depositos_pendentes_count'] ?></h3>
                    <p>Recargas Aguardando Aprovação</p>
                    <div style="font-size: 11px; opacity: 0.9;">
                        Lojistas solicitando saldo LC<br>
                        10% retido p/ fundo Nubank
                    </div>
                </div>
                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>
                <a href="<?= base_url('admin/linecash/depositos?status=pendente') ?>" class="small-box-footer">
                    Revisar Depósitos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Resgates PIX Pendentes -->
        <div class="col-lg-3 col-6">
            <div class="small-box <?= $metricas['resgates_pendentes_count'] > 0 ? 'bg-gradient-danger' : 'bg-gradient-secondary' ?>">
                <div class="inner">
                    <h3><?= $metricas['resgates_pendentes_count'] ?></h3>
                    <p>Fila de Resgates PIX</p>
                    <div style="font-size: 11px; opacity: 0.9;">
                        Prazo de transferência: até 24h<br>
                        2% de deságio retido
                    </div>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="<?= base_url('admin/linecash/resgates?status=pendente') ?>" class="small-box-footer">
                    Fila de Pagamentos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- PENDÊNCIAS URGENTES -->
    <div class="row">
        <!-- Recargas Pendentes de Validação -->
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-clock text-warning mr-1"></i> Recargas Pendentes de Validação
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/linecash/depositos') ?>" class="btn btn-sm btn-outline-warning">Ver Todas</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($depositosPendentes)): ?>
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-check-circle text-success fs-4 mb-2"></i><br>
                            Nenhuma recarga pendente no momento.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Lojista</th>
                                        <th>Valor Depósito</th>
                                        <th>Crédito LC (90%)</th>
                                        <th>Comissão BS (10%)</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($depositosPendentes as $dep): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($dep['lcc_nome']) ?></strong><br>
                                                <small class="text-muted"><?= esc($dep['lcc_cpf_cnpj']) ?></small>
                                            </td>
                                            <td>R$ <?= number_format((float)$dep['lcd_valor_reais'], 2, ',', '.') ?></td>
                                            <td><span class="badge badge-success"><?= number_format((float)$dep['lcd_valor_lc_creditado'], 2, ',', '.') ?> LC</span></td>
                                            <td><span class="badge badge-info">R$ <?= number_format((float)$dep['lcd_taxa_bs_reais'], 2, ',', '.') ?></span></td>
                                            <td>
                                                <a href="<?= base_url('admin/linecash/depositos/aprovar/' . $dep['lcd_id']) ?>" 
                                                   onclick="return confirm('Confirma aprovação do depósito de R$ <?= number_format((float)$dep['lcd_valor_reais'], 2, ',', '.') ?>?');"
                                                   class="btn btn-xs btn-success" title="Aprovar e Creditar LC">
                                                    <i class="fas fa-check"></i> Aprovar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resgates PIX Pendentes -->
        <div class="col-md-6">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-bolt text-danger mr-1"></i> Resgates PIX Aguardando Transferência (24h)
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/linecash/resgates') ?>" class="btn btn-sm btn-outline-danger">Ver Fila</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($resgatesPendentes)): ?>
                        <div class="p-3 text-center text-muted">
                            <i class="fas fa-check-circle text-success fs-4 mb-2"></i><br>
                            Nenhum resgate PIX pendente na fila.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Lojista</th>
                                        <th>Solicitado</th>
                                        <th>Líquido a Pagar (98%)</th>
                                        <th>Chave PIX</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resgatesPendentes as $res): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($res['lcc_nome']) ?></strong>
                                            </td>
                                            <td><?= number_format((float)$res['lcr_valor_lc'], 2, ',', '.') ?> LC</td>
                                            <td class="font-weight-bold text-success">
                                                R$ <?= number_format((float)$res['lcr_valor_liquido_reais'], 2, ',', '.') ?>
                                            </td>
                                            <td><code><?= esc($res['lcr_chave_pix']) ?></code></td>
                                            <td>
                                                <a href="<?= base_url('admin/linecash/resgates') ?>" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-external-link-alt"></i> Pagar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ÚLTIMAS TRANSAÇÕES NO LEDGER -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-secondary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-history text-muted mr-1"></i> Livro-Razão: Últimas 10 Movimentações em Tempo Real
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/linecash/extrato') ?>" class="btn btn-sm btn-outline-primary">Extrato Completo</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Data / Hora</th>
                                    <th>Tipo de Operação</th>
                                    <th>Origem</th>
                                    <th>Destino</th>
                                    <th>Valor (LC)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ultimasTransacoes)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3 text-muted">Nenhuma transação registrada até o momento.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ultimasTransacoes as $t): ?>
                                        <tr>
                                            <td>#<?= $t['lct_id'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($t['lct_criado_em'])) ?></td>
                                            <td>
                                                <?php switch ($t['lct_tipo']):
                                                    case 'cashback_venda': ?>
                                                        <span class="badge badge-success"><i class="fas fa-gift"></i> Cashback Venda</span>
                                                        <?php break;
                                                    case 'pagamento_compra': ?>
                                                        <span class="badge badge-primary"><i class="fas fa-shopping-cart"></i> Pagamento Compra</span>
                                                        <?php break;
                                                    case 'deposito_recarga': ?>
                                                        <span class="badge badge-info"><i class="fas fa-plus-circle"></i> Recarga Lojista</span>
                                                        <?php break;
                                                    case 'resgate_pix': ?>
                                                        <span class="badge badge-warning"><i class="fas fa-exchange-alt"></i> Resgate PIX</span>
                                                        <?php break;
                                                    default: ?>
                                                        <span class="badge badge-secondary"><?= esc($t['lct_tipo']) ?></span>
                                                <?php endswitch; ?>
                                            </td>
                                            <td><?= esc($t['origem_nome'] ?: 'Emissão Central BS') ?></td>
                                            <td><?= esc($t['destino_nome'] ?: '-') ?></td>
                                            <td class="font-weight-bold">
                                                <?= number_format((float)$t['lct_valor_lc'], 2, ',', '.') ?> LC
                                            </td>
                                            <td>
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check"></i> Concluída</span>
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
    </div>

</div>
<?= $this->endSection() ?>
