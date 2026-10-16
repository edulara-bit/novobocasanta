<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4 py-md-5">

    <!-- HERO HEADER -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4" style="background: linear-gradient(135deg, #162848 0%, #1e3a6c 60%, #cfa23e 150%); color: #fff;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                        <i class="fa-solid fa-gift me-1"></i> Programa de Recompensas
                    </span>
                    <h1 class="fw-black display-6 text-white mb-2">Clube Fidelidade & Cashback</h1>
                    <p class="lead text-white-50 mb-4" style="font-size: 1.1rem;">
                        Compre no comércio local, informe seu CPF e acumule carimbos, pontos e cashback para trocar por descontos e prêmios exclusivos!
                    </p>

                    <!-- FORMULÁRIO DE CONSULTA DE CPF -->
                    <form action="<?= base_url('fidelidade') ?>" method="GET" class="bg-white p-2 rounded-pill shadow d-flex align-items-center" style="max-width: 520px;">
                        <span class="ps-3 pe-2 text-muted"><i class="fa-solid fa-id-card fs-5"></i></span>
                        <input type="text" name="cpf" id="cpfConsulta" class="form-control border-0 shadow-none fw-bold" placeholder="Digite seu CPF (apenas números)" value="<?= esc($cpfBusca ?? '') ?>" required maxlength="14" style="font-size: 1.05rem;">
                        <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4 py-2 text-dark shadow-sm">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Consultar
                        </button>
                    </form>
                    <small class="text-white-50 mt-2 d-block">
                        <i class="fa-solid fa-shield-halved me-1"></i> Consulta 100% segura e protegida conforme a LGPD.
                    </small>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="p-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle p-4 border border-white border-opacity-25" style="width: 180px; height: 180px;">
                            <i class="fa-solid fa-coins display-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RESULTADOS DA CONSULTA -->
    <?php if ($buscou): ?>
        <?php if (!empty($lancamentos)): ?>
            <!-- CLIENTE ENCONTRADO COM LANÇAMENTOS -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white p-3 p-md-4 rounded-4 shadow-sm border">
                        <div>
                            <span class="badge bg-success-subtle text-success border px-3 py-1 rounded-pill mb-1">
                                <i class="fa-solid fa-circle-check me-1"></i> CPF Identificado
                            </span>
                            <h4 class="fw-bold text-dark mb-0">
                                Olá, <?= esc($cliente['cli_nome'] ?? 'Cliente Fidelidade') ?>!
                            </h4>
                            <small class="text-muted font-monospace">CPF: <?= esc(substr($cpfLimpo, 0, 3) . '.***.***-' . substr($cpfLimpo, -2)) ?></small>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="<?= base_url('fidelidade') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="fa-solid fa-arrow-rotate-left me-1"></i> Nova Consulta
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CARDS DE ESTATÍSTICAS -->
                <div class="col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 bg-white p-3 border-start border-4 border-warning">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center me-3" style="width: 55px; height: 55px;">
                                <i class="fa-solid fa-star fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase d-block">Pontos / Carimbos</small>
                                <h3 class="fw-black text-dark mb-0"><?= $saldoPontos ?> <span class="fs-6 fw-normal text-muted">pts</span></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 bg-white p-3 border-start border-4 border-success">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center me-3" style="width: 55px; height: 55px;">
                                <i class="fa-solid fa-wallet fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase d-block">Cashback Acumulado</small>
                                <h3 class="fw-black text-success mb-0">R$ <?= number_format($totalCashback, 2, ',', '.') ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 bg-white p-3 border-start border-4 border-primary">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3" style="width: 55px; height: 55px;">
                                <i class="fa-solid fa-bag-shopping fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase d-block">Total em Compras</small>
                                <h3 class="fw-black text-dark mb-0">R$ <?= number_format($totalCompras, 2, ',', '.') ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RESUMO POR PARCEIRO -->
                <?php if (!empty($resumoPorParceiro)): ?>
                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-store text-danger me-2"></i> Lojas Onde Você Pontuou
                        </h5>
                        <div class="list-group list-group-flush">
                            <?php foreach ($resumoPorParceiro as $pId => $pData): ?>
                                <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?= esc($pData['nome']) ?></h6>
                                        <small class="text-muted"><?= esc($pData['campanha']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                                            <?= $pData['pontos'] ?> Pontos
                                        </span>
                                        <?php if ($pData['cashback'] > 0): ?>
                                            <div class="small text-success fw-bold mt-1">R$ <?= number_format($pData['cashback'], 2, ',', '.') ?> cashback</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- HISTÓRICO DE COMPRAS E LANÇAMENTOS -->
                <div class="<?= !empty($resumoPorParceiro) ? 'col-lg-7' : 'col-12' ?>">
                    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Histórico de Compras Registradas
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Estabelecimento</th>
                                        <th>Valor Compra</th>
                                        <th>Campanha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lancamentos as $lan): ?>
                                        <tr>
                                            <td class="small text-muted">
                                                <?= date('d/m/Y', strtotime($lan['lan_data'])) ?>
                                            </td>
                                            <td>
                                                <strong class="text-dark"><?= esc($lan['par_nome'] ?? 'Parceiro') ?></strong>
                                                <?php if (!empty($lan['lan_descricao'])): ?>
                                                    <div class="small text-muted"><?= esc($lan['lan_descricao']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                R$ <?= number_format((float)$lan['lan_valor'], 2, ',', '.') ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= esc($lan['car_nome'] ?? 'Fidelidade') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- CPF NÃO ENCONTRADO OU SEM LANÇAMENTOS -->
            <div class="card border-0 rounded-4 shadow-sm bg-white p-4 p-md-5 text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-3 mx-auto" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-clipboard-question fs-2 text-warning"></i>
                </div>
                <h4 class="fw-bold text-dark">Nenhum ponto registrado para este CPF</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 550px;">
                    O CPF <strong><?= esc($cpfBusca) ?></strong> ainda não possui compras lançadas em nossa rede. Para começar a acumular pontos e cashback, basta informar seu CPF ao comprar em qualquer uma das lojas parceiras credenciadas!
                </p>
                <div>
                    <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/parceiros') ?>" class="btn btn-danger rounded-pill fw-bold px-4 py-2">
                        <i class="fa-solid fa-store me-1"></i> Ver Lojas Participantes
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- SEÇÃO EXPLICATIVA: COMO FUNCIONA -->
    <div class="row g-4 mt-2 mb-4">
        <div class="col-12 text-center">
            <h3 class="fw-black text-dark mb-1">Como Funciona o Clube Fidelidade?</h3>
            <p class="text-muted">É simples, gratuito e você economiza de verdade no comércio da sua cidade.</p>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 text-center">
                <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-cart-shopping fs-4"></i>
                </div>
                <h5 class="fw-bold text-dark">1. Compre no Comércio</h5>
                <p class="text-muted small mb-0">Visite qualquer loja ou estabelecimento credenciado no Boca Santa Ofertas na sua cidade.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 text-center">
                <div class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-fingerprint fs-4"></i>
                </div>
                <h5 class="fw-bold text-dark">2. Diga seu CPF</h5>
                <p class="text-muted small mb-0">No momento do pagamento, informe seu CPF ao lojista. A compra é registrada instantaneamente.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 text-center">
                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-trophy fs-4"></i>
                </div>
                <h5 class="fw-bold text-dark">3. Resgate Prêmios</h5>
                <p class="text-muted small mb-0">Ao completar sua meta de carimbos ou saldo de cashback, resgate produtos grátis e descontos exclusivos!</p>
            </div>
        </div>
    </div>

    <!-- BOTÕES DE CHAMADA -->
    <div class="text-center py-3">
        <a href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn btn-outline-dark rounded-pill px-4 fw-semibold me-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar para Início
        </a>
        <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/parceiros') ?>" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa-solid fa-store me-1"></i> Encontrar Lojas e Ofertas
        </a>
    </div>

</div>

<!-- SCRIPT MÁSCARA CPF -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cpfInput = document.getElementById('cpfConsulta');
    if (cpfInput) {
        cpfInput.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 9) {
                v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
            } else if (v.length > 6) {
                v = v.replace(/^(\d{3})(\d{3})(\d{1,3})$/, '$1.$2.$3');
            } else if (v.length > 3) {
                v = v.replace(/^(\d{3})(\d{1,3})$/, '$1.$2');
            }
            e.target.value = v;
        });
    }
});
</script>
<?= $this->endSection() ?>
