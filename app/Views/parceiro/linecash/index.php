<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="row g-4">

    <!-- CABEÇALHO & SALDOS -->
    <div class="col-12">
        <div class="bg-white p-4 rounded-4 shadow-sm">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill mb-2">
                        <i class="fa-solid fa-bolt me-1"></i> PDV Oficial LineCash
                    </span>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-coins text-warning me-2"></i> Caixa & Moeda de Fidelidade LineCash
                    </h3>
                    <p class="text-muted small mb-0">
                        Conceda cashback instantâneo aos seus clientes, receba pagamentos com saldo LineCash e gerencie suas recargas.
                    </p>
                </div>
                <!-- Box de Saldo LC da Loja -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="bg-light p-3 rounded-4 border text-end">
                        <span class="text-muted small d-block">Saldo Disponível no Caixa:</span>
                        <span class="fs-3 fw-bolder text-success">
                            <?= number_format((float)$conta['lcc_saldo'] - (float)$conta['lcc_saldo_bloqueado'], 2, ',', '.') ?> <small class="fs-6">LC</small>
                        </span>
                        <?php if ((float)$conta['lcc_saldo_bloqueado'] > 0): ?>
                            <small class="d-block text-warning fw-bold">
                                (<?= number_format((float)$conta['lcc_saldo_bloqueado'], 2, ',', '.') ?> LC em resgate)
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <button type="button" class="btn btn-warning fw-bold rounded-pill px-3 shadow-sm btn-sm" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('#tab-recarga-nav')).show()">
                            <i class="fa-solid fa-plus-circle me-1"></i> Fazer Recarga
                        </button>
                        <button type="button" class="btn btn-outline-danger fw-bold rounded-pill px-3 btn-sm" onclick="bootstrap.Tab.getOrCreateInstance(document.querySelector('#tab-resgate-nav')).show()">
                            <i class="fa-solid fa-money-bill-transfer me-1"></i> Resgatar PIX
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERTA DE COMPROVANTE WHATSAPP APÓS OPERAÇÃO -->
    <?php if (session()->getFlashdata('whatsapp_link')): ?>
        <div class="col-12">
            <div class="alert alert-success border-0 rounded-4 shadow-sm p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff;">
                <div>
                    <h5 class="fw-bold mb-1 text-white">
                        <i class="fa-brands fa-whatsapp fs-4 me-2"></i> Operação Concluída com Sucesso!
                    </h5>
                    <p class="mb-0 text-white opacity-90 small">
                        Deseja enviar o comprovante com o saldo acumulado e o produto destaque LineFast para o cliente <?= esc(session()->getFlashdata('whatsapp_cliente') ?: '') ?>?
                    </p>
                </div>
                <div>
                    <a href="<?= esc(session()->getFlashdata('whatsapp_link')) ?>" target="_blank" class="btn btn-light btn-lg rounded-pill fw-bold text-success px-4 shadow">
                        <i class="fa-brands fa-whatsapp text-success me-2 fs-5"></i> Enviar Comprovante no WhatsApp
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ABAS PRINCIPAIS DO PDV -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-bottom pt-3 px-4 pb-0">
                <ul class="nav nav-tabs border-0 gap-2" id="linecashTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3 px-3 text-dark border-0 border-bottom border-3 border-success" id="tab-cashback-nav" data-bs-toggle="tab" data-bs-target="#tab-cashback" type="button" role="tab">
                            <i class="fa-solid fa-gift text-success me-1"></i> 1. Dar Cashback (Venda)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 px-3 text-dark border-0" id="tab-receber-nav" data-bs-toggle="tab" data-bs-target="#tab-receber" type="button" role="tab">
                            <i class="fa-solid fa-shopping-cart text-primary me-1"></i> 2. Receber em LineCash
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 px-3 text-dark border-0" id="tab-recarga-nav" data-bs-toggle="tab" data-bs-target="#tab-recarga" type="button" role="tab">
                            <i class="fa-solid fa-circle-arrow-up text-warning me-1"></i> 3. Recarga de Saldo (10% BS)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 px-3 text-dark border-0" id="tab-resgate-nav" data-bs-toggle="tab" data-bs-target="#tab-resgate" type="button" role="tab">
                            <i class="fa-solid fa-money-bill-wave text-danger me-1"></i> 4. Resgate PIX (2% Deságio)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 px-3 text-dark border-0" id="tab-config-nav" data-bs-toggle="tab" data-bs-target="#tab-config" type="button" role="tab">
                            <i class="fa-solid fa-sliders text-info me-1"></i> 5. Vitrine LineFast
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3 px-3 text-dark border-0" id="tab-assinatura-nav" data-bs-toggle="tab" data-bs-target="#tab-assinatura" type="button" role="tab">
                            <i class="fa-solid fa-credit-card text-purple me-1"></i> 6. Assinatura (R$ 20/mês)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="linecashTabsContent">

                    <!-- ABA 1: CONCEDER CASHBACK NO PDV -->
                    <div class="tab-pane fade show active" id="tab-cashback" role="tabpanel">
                        <form action="<?= base_url('parceiro/linecash/cashback') ?>" method="post" id="formCashback">
                            <?= csrf_field() ?>

                            <div class="row g-3">
                                <!-- Coluna Esquerda: Dados da Compra -->
                                <div class="col-lg-6">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <h5 class="fw-bold text-dark mb-3">
                                            <i class="fa-solid fa-user-check text-success me-1"></i> Dados do Cliente & Compra
                                        </h5>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">CPF do Cliente <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="cpf" id="cpfCashback" class="form-control form-control-lg fw-bold" placeholder="000.000.000-00" required maxlength="14" autocomplete="off">
                                                <button class="btn btn-dark" type="button" id="btnBuscarCpfCashback">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            </div>
                                            <div id="statusCpfCliente" class="small mt-1"></div>
                                        </div>

                                        <div class="row g-2 mb-3" id="camposNovoCliente">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Nome do Cliente:</label>
                                                <input type="text" name="nome" id="nomeCliente" class="form-control" placeholder="Nome completo">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">WhatsApp do Cliente:</label>
                                                <input type="text" name="whatsapp" id="whatsappCliente" class="form-control" placeholder="(19) 99999-9999">
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Valor Total da Compra (R$) <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-white fw-bold">R$</span>
                                                    <input type="text" name="valor_compra" id="valorCompra" class="form-control fw-bold text-dark" placeholder="100,00" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">% Cashback <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-lg">
                                                    <input type="text" name="percentual" id="percentualCashback" class="form-control fw-bold text-success text-center" value="<?= number_format((float)($config['lcf_percentual_padrao'] ?? 1.00), 2, ',', '.') ?>" required>
                                                    <span class="input-group-text bg-white fw-bold">%</span>
                                                </div>
                                                <small class="text-muted" style="font-size: 11px;">Mínimo obrigatório: 1%</small>
                                            </div>
                                        </div>

                                        <!-- Card Resumo de Cálculo em Tempo Real -->
                                        <div class="alert alert-success border-0 rounded-4 d-flex justify-content-between align-items-center mb-0">
                                            <div>
                                                <span class="small d-block text-success fw-bold">Cashback a Creditar:</span>
                                                <span class="fs-4 fw-bolder text-success" id="calcValorLc">0,00 LC</span>
                                            </div>
                                            <div class="text-end text-muted small">
                                                1 LC = R$ 1,00<br>
                                                Debitado do caixa
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coluna Direita: Zona de Calor (Produtos) -->
                                <div class="col-lg-6">
                                    <div class="p-3 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="fw-bold text-dark mb-0">
                                                    <i class="fa-solid fa-fire text-danger me-1"></i> Zona de Calor (Cesta de Itens)
                                                </h5>
                                                <span class="badge bg-secondary">Opcional</span>
                                            </div>
                                            <p class="text-muted small mb-3">
                                                Informe até 3 principais produtos comprados para alimentar a inteligência de consumo e conversão da sua loja.
                                            </p>

                                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                                <div class="row g-2 mb-2 align-items-center">
                                                    <div class="col-4">
                                                        <input type="text" name="prod_codigo_<?= $i ?>" class="form-control form-control-sm" placeholder="Cód/Ref #<?= $i ?>">
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" name="prod_desc_<?= $i ?>" class="form-control form-control-sm" placeholder="Nome/Descrição do Item <?= $i ?>">
                                                    </div>
                                                </div>
                                            <?php endfor; ?>

                                            <?php if (!empty($config['lcf_produto_destaque_titulo'])): ?>
                                                <div class="alert alert-info border-0 rounded-3 mt-3 py-2 small mb-0">
                                                    <i class="fa-solid fa-link me-1"></i> Produto LineFast vinculado nas notificações: <strong><?= esc($config['lcf_produto_destaque_titulo']) ?></strong>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm py-3">
                                                <i class="fa-solid fa-check-circle me-2"></i> Confirmar Cashback no Caixa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ABA 2: RECEBER EM LINECASH (CLIENTE PAGA COM LC) -->
                    <div class="tab-pane fade" id="tab-receber" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <div class="p-4 bg-light rounded-4 border">
                                    <div class="text-center mb-4">
                                        <div class="avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle mx-auto d-inline-flex align-items-center justify-content-center p-3 mb-2">
                                            <i class="fa-solid fa-shopping-cart fs-2"></i>
                                        </div>
                                        <h4 class="fw-bold text-dark">Receber Pagamento em LineCash</h4>
                                        <p class="text-muted small mb-0">
                                            O cliente utiliza seus créditos acumulados para abater ou pagar o valor da compra.
                                        </p>
                                    </div>

                                    <div class="alert alert-primary border-0 rounded-4 py-2 small mb-4">
                                        <strong><i class="fa-solid fa-shield-halved"></i> Regra Oficial:</strong>
                                        A transferência mínima de pagamento do cliente é de <strong>100 LC (R$ 100,00)</strong>. É permitido pagamento misto (ex: 100 LC + restante no dinheiro ou cartão).
                                    </div>

                                    <form action="<?= base_url('parceiro/linecash/receber') ?>" method="post">
                                        <?= csrf_field() ?>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">CPF do Cliente Pagador <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <input type="text" name="cpf_pagamento" id="cpfReceber" class="form-control fw-bold" placeholder="000.000.000-00" required maxlength="14">
                                                <button class="btn btn-primary px-3" type="button" id="btnBuscarCpfReceber">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>
                                            </div>
                                            <div id="statusCpfReceber" class="small mt-1"></div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Valor em LineCash a Receber (Mínimo 100 LC) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <input type="text" name="valor_lc" class="form-control fw-bold text-primary" placeholder="100,00" required>
                                                <span class="input-group-text bg-white fw-bold">LC</span>
                                            </div>
                                            <small class="text-muted">1 LC = R$ 1,00 creditado imediatamente no seu saldo.</small>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3" onclick="return confirm('Confirma o débito no saldo LineCash do cliente?');">
                                            <i class="fa-solid fa-arrow-down-to-bracket me-2"></i> Confirmar Recebimento em LC
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ABA 3: RECARGA DE SALDO (10% COMISSÃO BS) -->
                    <div class="tab-pane fade" id="tab-recarga" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="p-4 bg-light rounded-4 border h-100">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fa-solid fa-qrcode text-warning me-1"></i> Dados para Depósito / PIX
                                    </h5>
                                    <p class="text-muted small">
                                        Faça o PIX ou depósito para a conta oficial do Boca Santa no <strong>Nubank</strong>. Após a confirmação pelo Administrador, você receberá <strong>90% do valor em LC</strong> no seu saldo de caixa para distribuir aos seus clientes.
                                    </p>

                                    <div class="bg-white p-3 rounded-4 border mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Banco:</span>
                                            <span class="fw-bold">Nubank (260)</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Chave PIX:</span>
                                            <code>bocasanta@nubank.com.br</code>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Favorecido:</span>
                                            <span class="fw-bold">Boca Santa Ofertas</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">Valor Mínimo:</span>
                                            <span class="badge bg-warning text-dark font-weight-bold">R$ 100,00</span>
                                        </div>
                                    </div>

                                    <form action="<?= base_url('parceiro/linecash/recarga') ?>" method="post">
                                        <?= csrf_field() ?>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Valor Transferido (R$) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">R$</span>
                                                <input type="text" name="valor_recarga" class="form-control form-control-lg fw-bold" placeholder="100,00" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Link do Comprovante (Opcional):</label>
                                            <input type="text" name="comprovante_url" class="form-control" placeholder="Link do comprovante ou envie no WhatsApp do Admin">
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Observações / Mensagem:</label>
                                            <input type="text" name="observacao" class="form-control" placeholder="Ex: PIX feito às 14h">
                                        </div>

                                        <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-2">
                                            <i class="fa-solid fa-paper-plane me-1"></i> Informar Recarga para Aprovação
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold text-dark mb-3">Histórico de Recargas</h5>
                                <div class="table-responsive bg-white rounded-4 border">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Data</th>
                                                <th>Valor (R$)</th>
                                                <th>Crédito (90%)</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($depositos)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">Nenhuma recarga registrada.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($depositos as $d): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y H:i', strtotime($d['lcd_criado_em'])) ?></td>
                                                        <td>R$ <?= number_format((float)$d['lcd_valor_reais'], 2, ',', '.') ?></td>
                                                        <td class="text-success fw-bold"><?= number_format((float)$d['lcd_valor_lc_creditado'], 2, ',', '.') ?> LC</td>
                                                        <td>
                                                            <?php if ($d['lcd_status'] === 'aprovado'): ?>
                                                                <span class="badge bg-success">Aprovado</span>
                                                            <?php elseif ($d['lcd_status'] === 'pendente'): ?>
                                                                <span class="badge bg-warning text-dark">Pendente</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Rejeitado</span>
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

                    <!-- ABA 4: RESGATE PIX (LC -> R$ COM 2% DESÁGIO) -->
                    <div class="tab-pane fade" id="tab-resgate" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="p-4 bg-light rounded-4 border h-100">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <i class="fa-solid fa-money-bill-transfer text-danger me-1"></i> Solicitar Resgate Financeiro PIX
                                    </h5>
                                    <p class="text-muted small">
                                        Transforme seus créditos LineCash em dinheiro na sua conta bancária via PIX.
                                    </p>

                                    <div class="alert alert-warning border-0 rounded-4 py-2 small mb-3">
                                        <strong><i class="fa-solid fa-clock"></i> Condições de Saque:</strong>
                                        Transferência mínima de <strong>100 LC</strong>. O sistema aplica <strong>2% de deságio</strong> e realiza a transferência PIX em até <strong>24 horas úteis</strong>.
                                    </div>

                                    <form action="<?= base_url('parceiro/linecash/resgate') ?>" method="post">
                                        <?= csrf_field() ?>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Saldo LC a Resgatar (Mínimo 100 LC) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="valor_resgate" id="valorResgate" class="form-control form-control-lg fw-bold" placeholder="100,00" required>
                                                <span class="input-group-text bg-white">LC</span>
                                            </div>
                                            <small class="text-muted" id="simulaResgate">Deságio 2%: R$ 0,00 | Líquido a receber: R$ 0,00</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sua Chave PIX <span class="text-danger">*</span></label>
                                            <input type="text" name="chave_pix" class="form-control form-control-lg fw-bold" placeholder="CPF, CNPJ, E-mail, Celular ou Aleatória" required>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Tipo da Chave:</label>
                                            <select name="tipo_chave" class="form-select">
                                                <option value="CNPJ/CPF">CNPJ / CPF</option>
                                                <option value="Telefone">Celular</option>
                                                <option value="E-mail">E-mail</option>
                                                <option value="Aleatoria">Chave Aleatória</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-3" onclick="return confirm('Confirma a solicitação de resgate PIX? O saldo será bloqueado até a confirmação.');">
                                            <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Solicitar Pagamento PIX (24h)
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="fw-bold text-dark mb-3">Histórico de Pedidos de Resgate</h5>
                                <div class="table-responsive bg-white rounded-4 border">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Data</th>
                                                <th>Solicitado</th>
                                                <th>Líquido (R$)</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($resgates)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">Nenhum resgate solicitado.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($resgates as $r): ?>
                                                    <tr>
                                                        <td><?= date('d/m/Y H:i', strtotime($r['lcr_criado_em'])) ?></td>
                                                        <td><?= number_format((float)$r['lcr_valor_lc'], 2, ',', '.') ?> LC</td>
                                                        <td class="text-success fw-bold">R$ <?= number_format((float)$r['lcr_valor_liquido_reais'], 2, ',', '.') ?></td>
                                                        <td>
                                                            <?php if ($r['lcr_status'] === 'pago'): ?>
                                                                <span class="badge bg-success">Pago</span>
                                                            <?php elseif ($r['lcr_status'] === 'pendente'): ?>
                                                                <span class="badge bg-warning text-dark">Em análise (24h)</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Cancelado</span>
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

                    <!-- ABA 5: CONFIGURAÇÕES & VITRINE LINEFAST -->
                    <div class="tab-pane fade" id="tab-config" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <form action="<?= base_url('parceiro/linecash/config') ?>" method="post">
                                    <?= csrf_field() ?>

                                    <div class="p-4 bg-light rounded-4 border">
                                        <h5 class="fw-bold text-dark mb-3">
                                            <i class="fa-solid fa-sliders text-info me-1"></i> Configurações de Cashback & Vitrine LineFast
                                        </h5>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">% Padrão de Cashback no Caixa:</label>
                                            <div class="input-group" style="max-width: 200px;">
                                                <input type="text" name="percentual_padrao" class="form-control fw-bold text-success text-center" value="<?= number_format((float)($config['lcf_percentual_padrao'] ?? 1.00), 2, ',', '.') ?>" required>
                                                <span class="input-group-text bg-white fw-bold">%</span>
                                            </div>
                                            <small class="text-muted">Valor sugerido automaticamente ao abrir o caixa do PDV (mín. 1%).</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Título do Produto em Destaque no LineFast:</label>
                                            <input type="text" name="produto_destaque_titulo" class="form-control" value="<?= esc($config['lcf_produto_destaque_titulo'] ?? '') ?>" placeholder="Ex: Picanha Black Angus Especial">
                                            <small class="text-muted">Aparece na mensagem de WhatsApp que o cliente recebe com o comprovante do cashback.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Link Direto do Produto no LineFast:</label>
                                            <input type="url" name="produto_destaque_url" class="form-control" value="<?= esc($config['lcf_produto_destaque_url'] ?? '') ?>" placeholder="https://linefast.com.br/produto/exemplo">
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Mensagem Customizada de Agradecimento:</label>
                                            <textarea name="mensagem_customizada" class="form-control" rows="2" placeholder="Obrigado por comprar conosco! Use seus LineCash na sua próxima visita."><?= esc($config['lcf_mensagem_customizada'] ?? '') ?></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-info text-white fw-bold rounded-pill px-4 py-2">
                                            <i class="fa-solid fa-floppy-disk me-1"></i> Salvar Configurações
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- ABA 6: ASSINATURA OPERACIONAL MERCADO PAGO (R$ 20/MÊS) -->
                    <div class="tab-pane fade" id="tab-assinatura" role="tabpanel">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="p-4 bg-light rounded-4 border">
                                    <div class="text-center mb-4">
                                        <div class="avatar-lg bg-info bg-opacity-10 text-info rounded-circle mx-auto d-inline-flex align-items-center justify-content-center p-3 mb-2">
                                            <i class="fa-solid fa-credit-card fs-2"></i>
                                        </div>
                                        <h4 class="fw-bold text-dark">Assinatura Operacional LineCash</h4>
                                        <p class="text-muted small mb-0">
                                            Cobrança mensal automatizada de <strong>R$ 20,00/mês</strong> via Mercado Pago para manutenção de infraestrutura, tráfego de mensagens e vitrine LineFast.
                                        </p>
                                    </div>

                                    <div class="bg-white p-4 rounded-4 border mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <span class="text-muted small d-block">Status Atual da Assinatura:</span>
                                                <?php if (!empty($assinatura) && $assinatura['lca_status'] === 'ativa'): ?>
                                                    <span class="badge bg-success fs-6 px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> Assinatura Ativa</span>
                                                <?php elseif (!empty($assinatura) && $assinatura['lca_status'] === 'inadimplente'): ?>
                                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="fa-solid fa-clock me-1"></i> Aguardando Confirmação</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary fs-6 px-3 py-2"><i class="fa-solid fa-circle-exclamation me-1"></i> Não Ativada</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-muted small d-block">Valor do Plano:</span>
                                                <span class="fs-4 fw-bolder text-dark">R$ 20,00 <small class="fs-6 text-muted">/ mês</small></span>
                                            </div>
                                        </div>

                                        <hr>

                                        <ul class="list-unstyled mb-0 small text-secondary">
                                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Cobrança recorrente automatizada via Cartão no Mercado Pago</li>
                                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Disparos de mensagens WhatsApp ilimitados para seus clientes</li>
                                            <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Vitrine integrada com links diretos para compras no LineFast</li>
                                            <li><i class="fa-solid fa-check text-success me-2"></i> Relatórios de hábitos de compra da sua loja</li>
                                        </ul>
                                    </div>

                                    <div class="text-center">
                                        <a href="<?= base_url('parceiro/linecash/assinar-mp') ?>" class="btn btn-primary btn-lg rounded-pill fw-bold px-5 shadow-sm py-3" style="background: #009ee3; border-color: #009ee3;">
                                            <i class="fa-solid fa-bolt me-2"></i> <?= (!empty($assinatura) && $assinatura['lca_status'] === 'ativa') ? 'Gerenciar no Mercado Pago' : 'Ativar Assinatura via Mercado Pago' ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- EXTRATO RECENTE DE TRANSAÇÕES NO CAIXA -->
    <div class="col-12">
        <div class="bg-white p-4 rounded-4 shadow-sm">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-clock-rotate-left text-muted me-1"></i> Extrato de Movimentações Recentes da sua Loja
            </h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Data / Hora</th>
                            <th>Operação</th>
                            <th>Origem / Destino</th>
                            <th>Valor (LC)</th>
                            <th>Descrição</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimasTransacoes)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Nenhuma movimentação realizada ainda no caixa.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ultimasTransacoes as $t): ?>
                                <tr>
                                    <td>#<?= $t['lct_id'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($t['lct_criado_em'])) ?></td>
                                    <td>
                                        <?php if ($t['lct_tipo'] === 'cashback_venda'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="fa-solid fa-gift"></i> Cashback Concedido</span>
                                        <?php elseif ($t['lct_tipo'] === 'pagamento_compra'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-cart-shopping"></i> Pagamento Recebido</span>
                                        <?php elseif ($t['lct_tipo'] === 'deposito_recarga'): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info"><i class="fa-solid fa-plus-circle"></i> Recarga</span>
                                        <?php elseif ($t['lct_tipo'] === 'resgate_pix'): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning"><i class="fa-solid fa-money-bill-transfer"></i> Resgate PIX</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= esc($t['lct_tipo']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int)$t['lct_origem_id'] === (int)$conta['lcc_id']): ?>
                                            <span class="text-danger fw-bold">Para:</span> <?= esc($t['destino_nome'] ?: 'Cliente') ?>
                                        <?php else: ?>
                                            <span class="text-success fw-bold">De:</span> <?= esc($t['origem_nome'] ?: 'Emissão Central') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold fs-6 <?= (int)$t['lct_origem_id'] === (int)$conta['lcc_id'] ? 'text-danger' : 'text-success' ?>">
                                        <?= (int)$t['lct_origem_id'] === (int)$conta['lcc_id'] ? '-' : '+' ?>
                                        <?= number_format((float)$t['lct_valor_lc'], 2, ',', '.') ?> LC
                                    </td>
                                    <td><small class="text-muted"><?= esc($t['lct_descricao']) ?></small></td>
                                    <td><span class="badge bg-success">Concluída</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- JAVASCRIPT REATIVO DO PDV -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const valorCompraInput = document.getElementById('valorCompra');
    const percentualInput = document.getElementById('percentualCashback');
    const calcValorLc = document.getElementById('calcValorLc');
    const cpfCashbackInput = document.getElementById('cpfCashback');
    const btnBuscarCpf = document.getElementById('btnBuscarCpfCashback');
    const statusCpfCliente = document.getElementById('statusCpfCliente');
    const nomeClienteInput = document.getElementById('nomeCliente');
    const whatsappClienteInput = document.getElementById('whatsappCliente');

    // Máscara e cálculo em tempo real do cashback
    function recalcularCashback() {
        const rawValor = (valorCompraInput.value || '0').replace(/\./g, '').replace(',', '.');
        const rawPerc = (percentualInput.value || '1').replace(',', '.');
        const valor = parseFloat(rawValor) || 0;
        const perc = parseFloat(rawPerc) || 1;
        const lc = (valor * (perc / 100)).toFixed(2);
        calcValorLc.textContent = lc.replace('.', ',') + ' LC';
    }

    if (valorCompraInput && percentualInput) {
        valorCompraInput.addEventListener('input', recalcularCashback);
        percentualInput.addEventListener('input', recalcularCashback);
    }

    // Busca AJAX de CPF do Cliente no Caixa
    function buscarCpf() {
        const cpf = (cpfCashbackInput.value || '').replace(/\D/g, '');
        if (cpf.length < 11) {
            statusCpfCliente.innerHTML = '<span class="text-muted">Digite os 11 dígitos do CPF.</span>';
            return;
        }

        statusCpfCliente.innerHTML = '<span class="text-primary"><i class="fa-solid fa-spinner fa-spin"></i> Consultando CPF...</span>';

        fetch('<?= base_url('parceiro/linecash/consultar-cpf') ?>/' + cpf)
            .then(res => res.json())
            .then(data => {
                if (data.found) {
                    statusCpfCliente.innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-check"></i> Cliente: ${data.nome} | Saldo Atual: ${data.saldo_formatado}</span>`;
                    if (nomeClienteInput && data.nome) nomeClienteInput.value = data.nome;
                    if (whatsappClienteInput && data.whatsapp) whatsappClienteInput.value = data.whatsapp;
                } else {
                    statusCpfCliente.innerHTML = `<span class="text-info"><i class="fa-solid fa-user-plus"></i> Novo cliente! Informe o nome e WhatsApp para cadastrar.</span>`;
                }
            })
            .catch(() => {
                statusCpfCliente.innerHTML = '<span class="text-warning">Não foi possível validar o CPF agora.</span>';
            });
    }

    if (btnBuscarCpf) btnBuscarCpf.addEventListener('click', buscarCpf);
    if (cpfCashbackInput) {
        cpfCashbackInput.addEventListener('blur', buscarCpf);
    }

    // Busca CPF no Recebimento
    const cpfReceberInput = document.getElementById('cpfReceber');
    const btnBuscarCpfReceber = document.getElementById('btnBuscarCpfReceber');
    const statusCpfReceber = document.getElementById('statusCpfReceber');

    function buscarCpfReceber() {
        const cpf = (cpfReceberInput.value || '').replace(/\D/g, '');
        if (cpf.length < 11) return;

        statusCpfReceber.innerHTML = '<span class="text-primary"><i class="fa-solid fa-spinner fa-spin"></i> Verificando saldo do cliente...</span>';
        fetch('<?= base_url('parceiro/linecash/consultar-cpf') ?>/' + cpf)
            .then(res => res.json())
            .then(data => {
                if (data.found) {
                    if (data.saldo >= 100) {
                        statusCpfReceber.innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-check-circle"></i> ${data.nome} | Saldo: ${data.saldo_formatado} (Apto para pagar!)</span>`;
                    } else {
                        statusCpfReceber.innerHTML = `<span class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation"></i> ${data.nome} possui apenas ${data.saldo_formatado}. Saldo insuficiente (mínimo 100 LC).</span>`;
                    }
                } else {
                    statusCpfReceber.innerHTML = `<span class="text-danger fw-bold">Cliente não localizado no LineCash.</span>`;
                }
            });
    }

    if (btnBuscarCpfReceber) btnBuscarCpfReceber.addEventListener('click', buscarCpfReceber);
    if (cpfReceberInput) cpfReceberInput.addEventListener('blur', buscarCpfReceber);

    // Simulação de Resgate PIX
    const valorResgateInput = document.getElementById('valorResgate');
    const simulaResgate = document.getElementById('simulaResgate');
    if (valorResgateInput && simulaResgate) {
        valorResgateInput.addEventListener('input', function() {
            const raw = (this.value || '0').replace(/\./g, '').replace(',', '.');
            const lc = parseFloat(raw) || 0;
            const desagio = (lc * 0.02).toFixed(2);
            const liq = (lc - desagio).toFixed(2);
            simulaResgate.textContent = `Deságio 2%: R$ ${desagio.replace('.', ',')} | Líquido a receber: R$ ${liq.replace('.', ',')}`;
        });
    }
});
</script>
<?= $this->endSection() ?>
