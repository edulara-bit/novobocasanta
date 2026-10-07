<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <!-- CABEÇALHO DA SEÇÃO -->
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-white p-4 rounded-4 shadow-sm">
            <div>
                <h3 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-stamp text-danger me-2"></i> Lançamento de Compras & Pontos do Cliente
                </h3>
                <p class="text-muted small mb-0">Registre as compras dos consumidores no seu estabelecimento para creditar pontos/carimbos no Cartão Fidelidade ou valide cupons de resgate de prêmios.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('parceiro/fidelidade') ?>" class="btn btn-outline-dark rounded-pill fw-bold px-3">
                    <i class="fa-solid fa-id-card me-1"></i> Ver Meus Cartões
                </a>
                <a href="<?= base_url('parceiro/fidelidade/criar') ?>" class="btn btn-danger rounded-pill fw-bold px-3">
                    <i class="fa-solid fa-plus-circle me-1"></i> Nova Campanha
                </a>
            </div>
        </div>
    </div>

    <!-- FORMULÁRIO DE LANÇAMENTO E RESGATE EM ABAS -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded-pill" id="fidelidadeTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold py-2" id="lancar-tab" data-bs-toggle="tab" data-bs-target="#tab-lancar" type="button" role="tab">
                            <i class="fa-solid fa-cart-plus me-1"></i> Lançar Compra
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold py-2 text-dark" id="resgate-tab" data-bs-toggle="tab" data-bs-target="#tab-resgate" type="button" role="tab">
                            <i class="fa-solid fa-gift me-1"></i> Resgatar Prêmio
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="fidelidadeTabContent">
                    <!-- ABA 1: LANÇAMENTO DE COMPRA -->
                    <div class="tab-pane fade show active" id="tab-lancar" role="tabpanel">
                        <?php if (empty($cartoes)): ?>
                            <div class="alert alert-warning border-0 rounded-4 text-center p-4">
                                <i class="fa-solid fa-circle-exclamation fs-3 text-warning mb-2"></i>
                                <h6 class="fw-bold">Nenhum Cartão Fidelidade Ativo</h6>
                                <p class="small text-muted mb-3">Você precisa criar pelo menos um Cartão Fidelidade para começar a pontuar seus clientes.</p>
                                <a href="<?= base_url('parceiro/fidelidade/criar') ?>" class="btn btn-danger btn-sm rounded-pill px-3">Criar Cartão Agora</a>
                            </div>
                        <?php else: ?>
                            <form action="<?= base_url('parceiro/fidelidade/salvar-lancamento') ?>" method="post" id="formLancamento">
                                <?= csrf_field() ?>

                                <!-- BUSCA CPF -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">
                                        CPF do Cliente <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" name="cli_cpf" id="cli_cpf" class="form-control form-control-lg fw-bold" placeholder="000.000.000-00" maxlength="14" value="<?= esc($cpfBusca ?? '') ?>" required autocomplete="off">
                                        <button class="btn btn-dark px-3" type="button" id="btnBuscarCpf">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </div>
                                    <div id="cpfStatus" class="small mt-1"></div>
                                </div>

                                <!-- DADOS DO CLIENTE (AUTO-PREENCHIDOS OU DIGITÁVEIS) -->
                                <div class="bg-light p-3 rounded-4 mb-3 border">
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-muted mb-1">Nome Completo do Cliente <span class="text-danger">*</span></label>
                                        <input type="text" name="cli_nome" id="cli_nome" class="form-control form-control-sm" placeholder="Nome do cliente" value="<?= esc($clienteEncontrado['cli_nome'] ?? '') ?>" required>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold text-muted mb-1">Celular / WhatsApp</label>
                                            <input type="text" name="cli_celular" id="cli_celular" class="form-control form-control-sm" placeholder="(00) 00000-0000" value="<?= esc($clienteEncontrado['cli_celular'] ?? '') ?>">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold text-muted mb-1">E-mail</label>
                                            <input type="email" name="cli_email" id="cli_email" class="form-control form-control-sm" placeholder="email@cliente.com" value="<?= esc($clienteEncontrado['cli_email'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div id="saldoInfo" class="mt-2 <?= !empty($clienteEncontrado) ? '' : 'd-none' ?>">
                                        <span class="badge bg-success-subtle text-success border">
                                            <i class="fa-solid fa-check-circle me-1"></i> Cliente com <strong><?= $saldoCartao ?></strong> compras/pontos registrados (Total: R$ <?= number_format($totalCompras, 2, ',', '.') ?>)
                                        </span>
                                    </div>
                                </div>

                                <!-- SELEÇÃO DO CARTÃO -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Cartão Fidelidade <span class="text-danger">*</span></label>
                                    <select name="lan_cartao" id="lan_cartao" class="form-select" required>
                                        <?php foreach ($cartoes as $cart): ?>
                                            <option value="<?= $cart['car_id'] ?>">
                                                <?= esc($cart['car_nome']) ?> (Meta: <?= $cart['car_pontos'] ?> Pontos)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- OFERTA ADQUIRIDA (OPCIONAL) -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark">Oferta Adquirida (Opcional)</label>
                                    <select name="lan_oferta" id="lan_oferta" class="form-select">
                                        <option value="0">Nenhuma oferta específica / Compra Geral</option>
                                        <?php if (!empty($ofertas)): ?>
                                            <?php foreach ($ofertas as $of): ?>
                                                <option value="<?= $of->pro_id ?>" data-preco="<?= $of->pro_precodesconto > 0 ? $of->pro_precodesconto : $of->pro_preco ?>">
                                                    <?= esc($of->pro_titulo) ?> (R$ <?= number_format((float)($of->pro_precodesconto > 0 ? $of->pro_precodesconto : $of->pro_preco), 2, ',', '.') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- VALOR DA COMPRA E CUPOM/NF -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-dark">Valor Compra (R$) <span class="text-danger">*</span></label>
                                        <input type="text" name="lan_valor" id="lan_valor" class="form-control form-control-lg fw-bold text-success" placeholder="0,00" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-dark">Nº Cupom / NF</label>
                                        <input type="text" name="lan_nf" id="lan_nf" class="form-control form-control-lg" placeholder="Ex: 12345">
                                    </div>
                                </div>

                                <!-- OBSERVAÇÃO / DESCRIÇÃO -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">Descrição dos Itens / Observação</label>
                                    <input type="text" name="lan_descricao" id="lan_descricao" class="form-control" placeholder="Ex: 1 Almoço executivo + 1 Bebida">
                                </div>

                                <button type="submit" class="btn btn-danger btn-lg w-100 rounded-pill fw-bold shadow-sm">
                                    <i class="fa-solid fa-stamp me-2"></i> Confirmar & Creditar Pontos
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- ABA 2: VALIDAÇÃO DE RESGATE -->
                    <div class="tab-pane fade" id="tab-resgate" role="tabpanel">
                        <form action="<?= base_url('parceiro/fidelidade/validar-resgate') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="text-center my-3">
                                <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle mb-3" style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-qrcode fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Validar Código de Resgate</h5>
                                <p class="text-muted small">Digite o código do voucher apresentado pelo cliente no aplicativo para confirmar a entrega do prêmio.</p>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark text-center w-100">Código do Cupom / Voucher</label>
                                <input type="text" name="res_codigo" class="form-control form-control-lg text-center fw-bold text-uppercase fs-4 letter-spacing-2" placeholder="EX: ABC1234" required autocomplete="off">
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm">
                                <i class="fa-solid fa-check-circle me-2"></i> Validar & Entregar Prêmio
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABELA DE LANÇAMENTOS RECENTES -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> Últimos Lançamentos Realizados
                </h5>
                <span class="badge bg-light text-dark border"><?= count($ultimosLancamentos) ?> registros</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Data/Hora</th>
                            <th>Cliente</th>
                            <th>Cartão / Oferta</th>
                            <th>Valor</th>
                            <th>NF/Cupom</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ultimosLancamentos)): ?>
                            <?php foreach ($ultimosLancamentos as $lan): ?>
                                <tr>
                                    <td>
                                        <span class="d-block fw-bold text-dark"><?= date('d/m/Y', strtotime($lan['lan_data'])) ?></span>
                                        <span class="text-muted small"><?= date('H:i', strtotime($lan['lan_data'])) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block"><?= esc($lan['cli_nome'] ?? 'Cliente') ?></strong>
                                        <span class="text-muted font-monospace small">CPF: <?= esc($lan['lan_cpf']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border d-block mb-1">
                                            <?= esc($lan['car_nome'] ?? 'Fidelidade') ?>
                                        </span>
                                        <?php if (!empty($lan['pro_titulo'])): ?>
                                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 140px;">
                                                <?= esc($lan['pro_titulo']) ?>
                                            </span>
                                        <?php elseif (!empty($lan['lan_descricao'])): ?>
                                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 140px;">
                                                <?= esc($lan['lan_descricao']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-success">R$ <?= number_format((float)$lan['lan_valor'], 2, ',', '.') ?></strong>
                                    </td>
                                    <td>
                                        <?= !empty($lan['lan_nf']) ? esc($lan['lan_nf']) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('parceiro/fidelidade/excluir-lancamento/' . $lan['lan_id']) ?>" class="btn btn-outline-danger btn-xs rounded-circle p-1" onclick="return confirm('Deseja estornar este lançamento de pontos?')" title="Estornar lançamento">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-receipt fs-2 mb-2 text-secondary d-block"></i>
                                    Nenhum lançamento registrado recentemente para a sua empresa.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCpf = document.getElementById('cli_cpf');
    const btnBuscar = document.getElementById('btnBuscarCpf');
    const inputNome = document.getElementById('cli_nome');
    const inputCelular = document.getElementById('cli_celular');
    const inputEmail = document.getElementById('cli_email');
    const statusDiv = document.getElementById('cpfStatus');
    const saldoDiv = document.getElementById('saldoInfo');
    const selectOferta = document.getElementById('lan_oferta');
    const inputValor = document.getElementById('lan_valor');

    function formatarCpf(v) {
        v = v.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        if (v.length > 9) {
            return v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
        } else if (v.length > 6) {
            return v.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
        } else if (v.length > 3) {
            return v.replace(/(\d{3})(\d{1,3})/, "$1.$2");
        }
        return v;
    }

    if (inputCpf) {
        inputCpf.addEventListener('input', function(e) {
            this.value = formatarCpf(this.value);
            const apenasNumeros = this.value.replace(/\D/g, '');
            if (apenasNumeros.length === 11) {
                buscarCliente(apenasNumeros);
            }
        });
    }

    if (btnBuscar && inputCpf) {
        btnBuscar.addEventListener('click', function() {
            const apenasNumeros = inputCpf.value.replace(/\D/g, '');
            if (apenasNumeros.length === 11) {
                buscarCliente(apenasNumeros);
            } else {
                alert('Digite os 11 dígitos do CPF para pesquisar.');
            }
        });
    }

    function buscarCliente(cpf) {
        if (statusDiv) statusDiv.innerHTML = '<span class="text-primary"><i class="fa-solid fa-spinner fa-spin me-1"></i> Buscando cliente...</span>';
        
        fetch('<?= base_url('parceiro/fidelidade/buscar-cliente') ?>?cpf=' + cpf)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'found' && data.cliente) {
                    if (inputNome) inputNome.value = data.cliente.nome || '';
                    if (inputCelular) inputCelular.value = data.cliente.celular || '';
                    if (inputEmail) inputEmail.value = data.cliente.email || '';
                    if (statusDiv) statusDiv.innerHTML = '<span class="text-success fw-bold"><i class="fa-solid fa-check me-1"></i> Cliente cadastrado: ' + (data.cliente.nome || 'Cliente') + '</span>';
                    if (saldoDiv) {
                        saldoDiv.classList.remove('d-none');
                        saldoDiv.innerHTML = '<span class="badge bg-success-subtle text-success border"><i class="fa-solid fa-star me-1"></i> Saldo atual: <strong>' + data.pontos + '</strong> compras/pontos registrados</span>';
                    }
                } else {
                    if (statusDiv) statusDiv.innerHTML = '<span class="text-warning fw-bold"><i class="fa-solid fa-user-plus me-1"></i> Novo cliente. Preencha o nome para cadastrar.</span>';
                    if (saldoDiv) saldoDiv.classList.add('d-none');
                }
            })
            .catch(() => {
                if (statusDiv) statusDiv.innerHTML = '';
            });
    }

    // Auto-preencher valor se selecionar oferta
    if (selectOferta && inputValor) {
        selectOferta.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const preco = selectedOpt.getAttribute('data-preco');
            if (preco && parseFloat(preco) > 0) {
                inputValor.value = parseFloat(preco).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
