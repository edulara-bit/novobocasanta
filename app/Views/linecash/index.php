<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- HERO DO LINECASH -->
    <div class="row align-items-center mb-5 g-4 py-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 28px; color: #fff; padding: 40px 20px;">
        <div class="col-lg-7 text-center text-lg-start px-lg-5">
            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3">
                <i class="fa-solid fa-bolt me-1"></i> Moeda Oficial de Cashback & Fidelidade
            </span>
            <h1 class="display-4 fw-bolder mb-3 text-white">
                Seu dinheiro de volta em <span class="text-warning">LineCash</span>.
            </h1>
            <p class="lead text-light opacity-90 mb-4" style="font-size: 1.15rem;">
                Compre no comércio local de Piracicaba e região, ganhe créditos em LineCash direto no seu CPF e utilize como desconto real em suas próximas compras!
            </p>
            <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start align-items-center">
                <div class="d-flex align-items-center gap-2 bg-white bg-opacity-10 px-3 py-2 rounded-pill border border-light border-opacity-25">
                    <i class="fa-solid fa-coins text-warning fs-5"></i>
                    <span class="fw-bold small">1 LC = R$ 1,00 em Compras</span>
                </div>
                <div class="d-flex align-items-center gap-2 bg-white bg-opacity-10 px-3 py-2 rounded-pill border border-light border-opacity-25">
                    <i class="fa-solid fa-shield-halved text-success fs-5"></i>
                    <span class="fw-bold small">Crédito Seguro no CPF</span>
                </div>
            </div>
        </div>

        <!-- FORMULÁRIO / CARD DE CONSULTA DO CONSUMIDOR -->
        <div class="col-lg-5 px-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-dark bg-white">
                <div class="card-body p-4">
                    <?php if (empty($conta)): ?>
                        <div class="text-center mb-4">
                            <div class="avatar-lg bg-warning bg-opacity-20 text-warning rounded-circle mx-auto d-inline-flex align-items-center justify-content-center p-3 mb-2">
                                <i class="fa-solid fa-wallet fs-2 text-dark"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Consulte Seu Saldo</h4>
                            <p class="text-muted small mb-0">Digite seu CPF para ver quantos LineCash você já tem acumulados para gastar.</p>
                        </div>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger py-2 small rounded-3"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('info')): ?>
                            <div class="alert alert-info py-2 small rounded-3"><?= session()->getFlashdata('info') ?></div>
                        <?php endif; ?>

                        <form action="<?= base_url('linecash/consultar') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Seu CPF:</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-id-card text-muted"></i></span>
                                    <input type="text" name="cpf" id="cpfConsumidor" class="form-control fw-bold" placeholder="000.000.000-00" required maxlength="14" autocomplete="off">
                                </div>
                                <small class="text-muted">Seus créditos ficam guardados com segurança no seu CPF.</small>
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold py-3 shadow-sm text-dark">
                                <i class="fa-solid fa-magnifying-glass me-2"></i> Ver Meus LineCash
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- SALDO ENCONTRADO DO CONSUMIDOR -->
                        <div class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">
                                <i class="fa-solid fa-check-circle me-1"></i> Carteira Ativa
                            </span>
                            <h5 class="fw-bold text-dark mb-0"><?= esc($conta['lcc_nome']) ?></h5>
                            <small class="text-muted d-block mb-3">CPF: ***.<?= substr($conta['lcc_cpf_cnpj'], 3, 3) ?>.<?= substr($conta['lcc_cpf_cnpj'], 6, 3) ?>-**</small>

                            <div class="p-3 bg-light rounded-4 border mb-3">
                                <span class="text-muted small d-block">Seu Saldo Disponível para Compras:</span>
                                <span class="display-5 fw-bolder text-success d-block my-1">
                                    <?= number_format((float)$conta['lcc_saldo'], 2, ',', '.') ?> <small class="fs-4">LC</small>
                                </span>
                                <span class="badge bg-dark px-3 py-1 rounded-pill fw-bold">
                                    Equivalente a R$ <?= number_format((float)$conta['lcc_saldo'], 2, ',', '.') ?>
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="#extrato-cliente" class="btn btn-outline-dark btn-sm rounded-pill w-100 py-2 fw-bold">
                                    <i class="fa-solid fa-receipt me-1"></i> Ver Extrato
                                </a>
                                <a href="<?= base_url('linecash/sair') ?>" class="btn btn-outline-danger btn-sm rounded-pill py-2 px-3 fw-bold" title="Sair da consulta">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SE TIVER CARTEIRA CONSULTADA: EXTRATO DO CONSUMIDOR -->
    <?php if (!empty($conta)): ?>
        <div class="row mb-5" id="extrato-cliente">
            <div class="col-12">
                <div class="bg-white p-4 rounded-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-receipt text-warning me-2"></i> Extrato de Movimentações da sua Carteira
                        </h4>
                        <span class="badge bg-light text-dark border">Atualizado em tempo real</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo de Operação</th>
                                    <th>Estabelecimento</th>
                                    <th>Valor (LC)</th>
                                    <th>Valor da Compra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($extrato)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Nenhuma transação registrada nesta carteira.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($extrato as $t): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($t['lct_criado_em'])) ?></td>
                                            <td>
                                                <?php if ($t['lct_tipo'] === 'cashback_venda'): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="fa-solid fa-gift me-1"></i> Cashback Ganho</span>
                                                <?php elseif ($t['lct_tipo'] === 'pagamento_compra'): ?>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-cart-shopping me-1"></i> Pagamento de Compra</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= esc($t['lct_tipo']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= esc($t['origem_nome'] ?: $t['destino_nome'] ?: 'Loja Parceira') ?></strong>
                                            </td>
                                            <td class="fw-bold fs-6 <?= (int)$t['lct_origem_id'] === (int)$conta['lcc_id'] ? 'text-danger' : 'text-success' ?>">
                                                <?= (int)$t['lct_origem_id'] === (int)$conta['lcc_id'] ? '-' : '+' ?>
                                                <?= number_format((float)$t['lct_valor_lc'], 2, ',', '.') ?> LC
                                            </td>
                                            <td>
                                                <?= !empty($t['lct_valor_compra_reais']) ? 'R$ ' . number_format((float)$t['lct_valor_compra_reais'], 2, ',', '.') : '-' ?>
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
    <?php endif; ?>

    <!-- COMO FUNCIONA O LINECASH -->
    <div class="row g-4 mb-5">
        <div class="col-12 text-center mb-2">
            <h3 class="fw-bold text-dark">Como Funciona o LineCash?</h3>
            <p class="text-muted">Simples, transparente e direto na hora da sua compra.</p>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="avatar-lg bg-warning bg-opacity-20 text-warning rounded-circle mx-auto d-inline-flex align-items-center justify-content-center p-3 mb-3">
                    <i class="fa-solid fa-store fs-2 text-dark"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">1. Compre nas Lojas Parceiras</h5>
                <p class="text-muted small mb-0">Faça suas compras normais no comércio participante e informe seu CPF no caixa.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="avatar-lg bg-success bg-opacity-20 text-success rounded-circle mx-auto d-inline-flex align-items-center justify-content-center p-3 mb-3">
                    <i class="fa-solid fa-gift fs-2 text-success"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">2. Ganhe Cashback em LC</h5>
                <p class="text-muted small mb-0">Você recebe na hora LineCash (1 LC = R$ 1,00) e é notificado no WhatsApp com seu saldo e ofertas.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                <div class="avatar-lg bg-primary bg-opacity-20 text-primary rounded-circle mx-auto d-inline-flex align-items-center justify-content-center p-3 mb-3">
                    <i class="fa-solid fa-cart-shopping fs-2 text-primary"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">3. Pague Suas Compras</h5>
                <p class="text-muted small mb-0">Acumulando a partir de 100 LC, você pode pagar suas compras utilizando o saldo LineCash diretamente no caixa!</p>
            </div>
        </div>
    </div>

    <!-- LOJAS PARCEIRAS ONDE USAR -->
    <div class="row g-4 mb-5">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-store text-danger me-2"></i> Lojas Parceiras Participantes
                </h3>
                <p class="text-muted small mb-0">Estabelecimentos onde você pontua e resgata créditos LineCash.</p>
            </div>
            <a href="<?= base_url('linecash/lojas') ?>" class="btn btn-outline-dark btn-sm rounded-pill fw-bold px-3">
                Ver Todas as Lojas
            </a>
        </div>

        <?php if (!empty($lojas)): ?>
            <?php foreach ($lojas as $l): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                        <?php if (!empty($l->par_imagem)): ?>
                            <img src="<?= base_url('assets/uploads/parceiros/' . $l->par_imagem) ?>" alt="<?= esc($l->getNome()) ?>" class="img-fluid rounded-3 mx-auto mb-3" style="max-height: 70px; object-fit: contain;">
                        <?php else: ?>
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3" style="height: 70px; width: 70px;">
                                <i class="fa-solid fa-store fs-3 text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?= esc($l->getNome()) ?>">
                            <?= esc($l->getNome()) ?>
                        </h6>
                        <small class="text-muted d-block mb-3"><?= esc($l->par_bairro ?: 'Piracicaba') ?></small>

                        <div class="mt-auto">
                            <?php if (!empty($l->par_whatsapp)): ?>
                                <a href="https://api.whatsapp.com/send?phone=55<?= preg_replace('/\D/', '', $l->par_whatsapp) ?>" target="_blank" class="btn btn-success btn-sm w-100 rounded-pill fw-bold">
                                    <i class="fa-brands fa-whatsapp me-1"></i> Falar no WhatsApp
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-dark w-100 py-2">Loja Participante</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- SCRIPT MÁSCARA CPF -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpfConsumidor');
    if (cpfInput) {
        cpfInput.addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 9) {
                v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            } else if (v.length > 6) {
                v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (v.length > 3) {
                v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            }
            this.value = v;
        });
    }
});
</script>
<?= $this->endSection() ?>
