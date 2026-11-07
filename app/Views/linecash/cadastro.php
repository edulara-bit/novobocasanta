<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4 py-md-5">

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <?php 
            $parceiroNome = '';
            $parceiroLogo = '';
            $parceiroId = '';
            if (!empty($parceiroIndicador)) {
                if (is_object($parceiroIndicador)) {
                    $parceiroNome = method_exists($parceiroIndicador, 'getNome') ? $parceiroIndicador->getNome() : ($parceiroIndicador->par_nome ?? 'Loja Parceira');
                    $parceiroLogo = method_exists($parceiroIndicador, 'getLogoUrl') ? $parceiroIndicador->getLogoUrl() : '';
                    $parceiroId = $parceiroIndicador->par_id ?? 0;
                } else {
                    $parceiroNome = $parceiroIndicador['par_nome'] ?? $parceiroIndicador['par_fantasia'] ?? 'Loja Parceira';
                    $rawLogo = $parceiroIndicador['par_imagem'] ?? '';
                    if (!empty($rawLogo)) {
                        if (str_starts_with($rawLogo, 'http://') || str_starts_with($rawLogo, 'https://')) {
                            $parceiroLogo = $rawLogo;
                        } elseif (file_exists(FCPATH . 'upimg/parceiros/' . $rawLogo)) {
                            $parceiroLogo = base_url('upimg/parceiros/' . $rawLogo);
                        } else {
                            $parceiroLogo = "https://www.bocasantaofertas.com.br/upimg/parceiros/{$rawLogo}";
                        }
                    }
                    $parceiroId = $parceiroIndicador['par_id'] ?? 0;
                }
            }
            ?>

            <!-- CARD DE CONVITE DO LOJISTA (SE HOUVER) -->
            <?php if (!empty($parceiroIndicador)): ?>
                <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
                    <div class="card-body p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle p-2 shadow-sm mb-3" style="width: 76px; height: 76px;">
                            <?php if (!empty($parceiroLogo)): ?>
                                <img src="<?= $parceiroLogo ?>" alt="<?= esc($parceiroNome) ?>" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?= base_url('assets/images/logo.png') ?>'">
                            <?php else: ?>
                                <i class="fa-solid fa-store fs-2 text-primary"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">
                                <i class="fa-solid fa-gift me-1"></i> Convite Especial
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-1">
                            <?= esc($parceiroNome) ?>
                        </h4>
                        <p class="text-white-50 small mb-0 px-md-4">
                            Convidou você para ganhar <strong>Cashback e Descontos Reais</strong> pelo LineCash em todas as suas compras!
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- FORMULÁRIO PRINCIPAL DE CADASTRO -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-20 text-warning rounded-circle p-3 mb-2">
                        <i class="fa-solid fa-coins fs-2 text-dark"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Crie sua Carteira LineCash</h3>
                    <p class="text-muted small mb-0">Cadastre-se gratuitamente e acumule saldo em dinheiro de volta direto no seu CPF.</p>
                </div>

                <div class="card-body p-4">

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger py-2 rounded-3 small">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('linecash/cadastrar') ?>" method="POST" id="formCadastroCliente">
                        <?= csrf_field() ?>

                        <?php if (!empty($parceiroId)): ?>
                            <input type="hidden" name="parceiro_id" value="<?= esc($parceiroId) ?>">
                        <?php endif; ?>

                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-user text-primary me-1"></i> Dados Pessoais
                        </h6>

                        <!-- Nome -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Nome Completo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="nome" id="cadNome" class="form-control" placeholder="Digite seu nome completo" value="<?= old('nome') ?>" required autocomplete="name">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- CPF -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">CPF <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" name="cpf" id="cadCpf" class="form-control fw-bold" placeholder="000.000.000-00" value="<?= old('cpf') ?>" required maxlength="14" autocomplete="off">
                                </div>
                                <small class="text-muted" style="font-size: 11px;">O seu saldo ficará seguro no seu documento.</small>
                            </div>

                            <!-- WhatsApp -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">WhatsApp / Celular <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-brands fa-whatsapp text-success fs-5"></i></span>
                                    <input type="text" name="whatsapp" id="cadWhatsapp" class="form-control fw-bold" placeholder="(19) 99999-9999" value="<?= old('whatsapp') ?>" required maxlength="15" autocomplete="tel">
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Para você receber notificações de cashback.</small>
                            </div>
                        </div>

                        <!-- E-mail (Opcional) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">E-mail <span class="text-muted fw-normal">(Opcional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="email" id="cadEmail" class="form-control" placeholder="seuemail@exemplo.com" value="<?= old('email') ?>" autocomplete="email">
                            </div>
                        </div>

                        <!-- ENDEREÇO E LOCALIZAÇÃO -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-map-location-dot text-danger me-1"></i> Endereço de Localização
                        </h6>

                        <!-- CEP com Busca Automática -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark small">CEP <span class="text-muted fw-normal">(Preenchimento rápido)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-mail-bulk"></i></span>
                                    <input type="text" name="cep" id="cadCep" class="form-control fw-bold" placeholder="00000-000" maxlength="9" value="<?= old('cep') ?>">
                                </div>
                                <div id="statusCep" class="small mt-1 text-muted" style="font-size: 11px;"></div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark small">Bairro</label>
                                <input type="text" name="bairro" id="cadBairro" class="form-control" placeholder="Seu bairro" value="<?= old('bairro') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-9">
                                <label class="form-label fw-bold text-dark small">Endereço / Logradouro</label>
                                <input type="text" name="endereco" id="cadEndereco" class="form-control" placeholder="Rua, Avenida, Praça..." value="<?= old('endereco') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark small">Número</label>
                                <input type="text" name="numero" id="cadNumero" class="form-control" placeholder="123" value="<?= old('numero') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Complemento <span class="text-muted fw-normal">(Opcional)</span></label>
                                <input type="text" name="complemento" id="cadComplemento" class="form-control" placeholder="Apto, Bloco, Casa..." value="<?= old('complemento') ?>">
                            </div>
                            <div class="col-md-4 col-8">
                                <label class="form-label fw-bold text-dark small">Cidade</label>
                                <input type="text" name="cidade" id="cadCidade" class="form-control" placeholder="Piracicaba" value="<?= old('cidade', 'Piracicaba') ?>">
                            </div>
                            <div class="col-md-2 col-4">
                                <label class="form-label fw-bold text-dark small">UF</label>
                                <input type="text" name="estado" id="cadEstado" class="form-control text-uppercase text-center" placeholder="SP" maxlength="2" value="<?= old('estado', 'SP') ?>">
                            </div>
                        </div>

                        <!-- Vantagens -->
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="d-flex align-items-center gap-2 mb-2 text-dark small fw-bold">
                                <i class="fa-solid fa-circle-check text-success"></i> 1 LineCash (LC) = R$ 1,00 de desconto
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-2 text-dark small fw-bold">
                                <i class="fa-solid fa-circle-check text-success"></i> Válido em dezenas de lojas e comércios parceiros
                            </div>
                            <div class="d-flex align-items-center gap-2 text-dark small fw-bold">
                                <i class="fa-solid fa-circle-check text-success"></i> 100% gratuito e protegido pela LGPD
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100 rounded-pill fw-bold text-dark shadow-sm py-3 mb-3">
                            <i class="fa-solid fa-bolt me-2"></i> Ativar Minha Carteira Grátis
                        </button>

                        <div class="text-center">
                            <span class="text-muted small">Já possui cadastro?</span>
                            <a href="<?= base_url('linecash') ?>" class="text-decoration-none fw-bold text-dark small ms-1">
                                Consulte seu saldo aqui <i class="fa-solid fa-arrow-right small"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- GARANTIA E SEGURANÇA -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Seus dados estão seguros. Utilizamos padrões rígidos em conformidade com a LGPD.
                </small>
            </div>

        </div>
    </div>

</div>

<!-- MÁSCARAS DE ENTRADA & VIACEP JAVASCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cadCpf');
    const whatsInput = document.getElementById('cadWhatsapp');
    const cepInput = document.getElementById('cadCep');
    const statusCep = document.getElementById('statusCep');
    const enderecoInput = document.getElementById('cadEndereco');
    const bairroInput = document.getElementById('cadBairro');
    const cidadeInput = document.getElementById('cadCidade');
    const estadoInput = document.getElementById('cadEstado');
    const numeroInput = document.getElementById('cadNumero');

    // Máscara CPF
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 9) {
                e.target.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            } else if (v.length > 6) {
                e.target.value = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (v.length > 3) {
                e.target.value = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            } else {
                e.target.value = v;
            }
        });
    }

    // Máscara WhatsApp
    if (whatsInput) {
        whatsInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 10) {
                e.target.value = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (v.length > 6) {
                e.target.value = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (v.length > 2) {
                e.target.value = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
            } else {
                e.target.value = v;
            }
        });
    }

    // Máscara e Busca Automática ViaCEP
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 8) v = v.substring(0, 8);
            if (v.length > 5) {
                e.target.value = v.replace(/(\d{5})(\d{1,3})/, '$1-$2');
            } else {
                e.target.value = v;
            }

            if (v.length === 8) {
                buscarViaCep(v);
            }
        });

        cepInput.addEventListener('blur', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length === 8) {
                buscarViaCep(v);
            }
        });
    }

    function buscarViaCep(cep) {
        if (!statusCep) return;
        statusCep.innerHTML = '<span class="text-primary"><i class="fa-solid fa-spinner fa-spin"></i> Localizando endereço...</span>';

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(res => res.json())
            .then(data => {
                if (data.erro) {
                    statusCep.innerHTML = '<span class="text-danger">CEP não encontrado. Digite o endereço manualmente.</span>';
                } else {
                    statusCep.innerHTML = '<span class="text-success"><i class="fa-solid fa-check"></i> Endereço preenchido automaticamente!</span>';
                    if (enderecoInput && data.logradouro) enderecoInput.value = data.logradouro;
                    if (bairroInput && data.bairro) bairroInput.value = data.bairro;
                    if (cidadeInput && data.localidade) cidadeInput.value = data.localidade;
                    if (estadoInput && data.uf) estadoInput.value = data.uf;
                    if (numeroInput) numeroInput.focus();
                }
            })
            .catch(() => {
                statusCep.innerHTML = '<span class="text-warning">Não foi possível consultar o CEP online.</span>';
            });
    }
});
</script>
<?= $this->endSection() ?>
