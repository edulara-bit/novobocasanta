<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">

    <!-- HERO ANUNCIE -->
    <div class="card border-0 shadow-sm rounded-4 p-5 mb-5 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #b91c1c 100%);">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3">🚀 TURBINE SUAS VENDAS LOCAIS</span>
                <h1 class="display-5 fw-extrabold mb-3">Encontrou este produto no Google? Seu cliente também pode encontrar você!</h1>
                <p class="lead text-white-50 mb-4">
                    O <strong>Boca Santa Ofertas</strong> é o maior portal de divulgação e vendas locais de Piracicaba e região. Tenha suas ofertas indexadas nos primeiros lugares das buscas e conecte sua loja ao <strong>Linefast</strong> para vendas instantâneas.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#planosAnuncio" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 text-dark shadow-sm">
                        <i class="fa-solid fa-tags me-2"></i> Ver Planos & Preços
                    </a>
                    <a href="#formularioAnuncio" class="btn btn-outline-light btn-lg fw-bold rounded-pill px-4">
                        <i class="fa-solid fa-paper-plane me-2"></i> Cadastrar Empresa
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <img src="<?= base_url('assets/images/encontrou.jpg') ?>" alt="Anuncie no Boca Santa" class="img-fluid rounded-4 shadow-lg" style="max-height: 280px;" onerror="this.src='<?= base_url('assets/images/img-megafone.png') ?>'">
            </div>
        </div>
    </div>

    <!-- BENEFÍCIOS -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5 text-center">
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="fs-1 text-danger mb-3"><i class="fa-brands fa-google"></i></div>
                <h4 class="fw-bold mb-2">Primeiras Posições no Google</h4>
                <p class="text-muted small mb-0">Todas as ofertas e produtos são otimizados com SEO de alta performance e Schema.org para atrair clientes que já estão procurando o que você vende.</p>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="fs-1 text-primary mb-3"><i class="fa-solid fa-bolt"></i></div>
                <h4 class="fw-bold mb-2">Integração com Linefast</h4>
                <p class="text-muted small mb-0">Seus produtos em estoque no Linefast aparecem automaticamente na vitrine com botão de compra direto para o carrinho.</p>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="fs-1 text-success mb-3"><i class="fa-brands fa-whatsapp"></i></div>
                <h4 class="fw-bold mb-2">Leads Direto no WhatsApp</h4>
                <p class="text-muted small mb-0">Botão inteligente que envia o cliente interessado com mensagem personalizada diretamente para o WhatsApp da sua empresa.</p>
            </div>
        </div>
    </div>

    <!-- SEÇÃO DE PLANOS -->
    <div class="mb-5" id="planosAnuncio">
        <div class="text-center mb-4">
            <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-2 rounded-pill mb-2">INVESTIMENTO ACESSÍVEL</span>
            <h2 class="fw-extrabold text-dark">Planos Sob Medida Para o Seu Negócio</h2>
            <p class="text-muted">Divulgue seus produtos e receba contatos diários de clientes qualificados em Piracicaba e região</p>
        </div>

        <div class="row row-cols-1 row-cols-lg-3 g-4">
            <!-- PLANO BÁSICO MENSAL -->
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column">
                    <span class="badge bg-secondary-subtle text-secondary align-self-start px-3 py-1 rounded-pill fw-bold mb-2">PLANO BÁSICO</span>
                    <h4 class="fw-bold mb-1">Básico Mensal</h4>
                    <p class="text-muted small mb-2">Catálogo virtual completo para sua loja</p>
                    <div class="display-6 fw-extrabold text-dark my-3">R$ 75<small class="fs-6 text-muted">/mês</small></div>
                    <ul class="list-unstyled small text-secondary lh-lg mb-4 flex-grow-1">
                        <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Até 60 produtos cadastrados</strong> (fotos, preços e descrições)</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Página exclusiva da empresa no portal (seu site individual)</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Botão direto para WhatsApp da sua loja</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Indexação SEO no Google e buscadores</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Compartilhamento profissional no Facebook</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Inclua, altere e divulgue da maneira que desejar</li>
                    </ul>
                    <a href="#formularioAnuncio" onclick="selecionarPlano('Plano Básico Mensal (60 produtos)')" class="btn btn-outline-dark rounded-pill fw-bold w-100 py-2">
                        Escolher Plano Básico
                    </a>
                </div>
            </div>

            <!-- PLANO DESTAQUE LOCAL -->
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column">
                    <span class="badge bg-danger-subtle text-danger align-self-start px-3 py-1 rounded-pill fw-bold mb-2">DESTAQUE LOCAL</span>
                    <h4 class="fw-bold mb-1">Destaque Local</h4>
                    <p class="text-muted small mb-2">Mais visibilidade e alcance na sua categoria</p>
                    <div class="display-6 fw-extrabold text-dark my-3">R$ 99<small class="fs-6 text-muted">/mês</small></div>
                    <ul class="list-unstyled small text-secondary lh-lg mb-4 flex-grow-1">
                        <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Tudo do Plano Básico (60 produtos)</strong></li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Destaque rotativo na sua cidade e categoria</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Selo de Parceiro Verificado Boca Santa</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Perfil institucional com fotos e mapas</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Suporte comercial dedicado</li>
                    </ul>
                    <a href="#formularioAnuncio" onclick="selecionarPlano('Plano Destaque Local')" class="btn btn-outline-danger rounded-pill fw-bold w-100 py-2">
                        Escolher Destaque
                    </a>
                </div>
            </div>

            <!-- PLANO SUPER DESTAQUE -->
            <div class="col">
                <div class="card border-2 border-danger shadow rounded-4 p-4 bg-white h-100 d-flex flex-column position-relative">
                    <span class="position-absolute top-0 end-0 m-3 badge bg-danger px-3 py-1 rounded-pill fw-bold">MAIS COMPLETO</span>
                    <span class="badge bg-primary-subtle text-primary align-self-start px-3 py-1 rounded-pill fw-bold mb-2">COM LINEFAST</span>
                    <h4 class="fw-bold mb-1">Super Destaque</h4>
                    <p class="text-muted small mb-2">Sincronização de estoque e vendas automáticas</p>
                    <div class="display-6 fw-extrabold text-danger my-3">R$ 179<small class="fs-6 text-muted">/mês</small></div>
                    <ul class="list-unstyled small text-secondary lh-lg mb-4 flex-grow-1">
                        <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Ofertas e produtos ilimitados</strong></li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Posição prioritária na Home e Topo das buscas</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Sincronização automática com Linefast</strong></li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Deep link de compras direto no carrinho</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i> Atendimento e suporte prioritário VIP</li>
                    </ul>
                    <a href="#formularioAnuncio" onclick="selecionarPlano('Plano Super Destaque (com Linefast)')" class="btn btn-danger rounded-pill fw-bold w-100 py-2 shadow-sm">
                        Escolher Super Destaque
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FORMULÁRIO E ATENDIMENTO DIRETO -->
    <div class="row g-4" id="formularioAnuncio">
        <!-- FORMULÁRIO -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                <h3 class="fw-bold text-dark mb-2"><i class="fa-solid fa-store text-danger me-2"></i> Cadastre sua Empresa</h3>
                <p class="text-muted small mb-4">Preencha os dados abaixo para nossa equipe ativar sua conta e cadastrar suas ofertas:</p>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center">
                        <i class="fa-solid fa-circle-check fs-4 me-3 text-success"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger rounded-3 mb-4 d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-danger"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('anuncie/enviar') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do Responsável <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control rounded-3" placeholder="Seu nome completo" value="<?= old('nome') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome da Empresa / Comércio <span class="text-danger">*</span></label>
                        <input type="text" name="empresa" class="form-control rounded-3" placeholder="Nome Fantasia da sua loja" value="<?= old('empresa') ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="contato@suaempresa.com" value="<?= old('email') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">WhatsApp / Telefone <span class="text-danger">*</span></label>
                            <input type="text" name="telefone" class="form-control rounded-3" placeholder="(19) 99999-9999" value="<?= old('telefone') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cidade <span class="text-danger">*</span></label>
                            <input type="text" name="cidade" class="form-control rounded-3" value="<?= old('cidade', esc($cidadeAtual['cid_nome'] ?? 'Piracicaba')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Plano de Interesse</label>
                            <select name="plano" id="selectPlano" class="form-select rounded-3">
                                <option value="Plano Básico Mensal (60 produtos)" <?= old('plano') === 'Plano Básico Mensal (60 produtos)' ? 'selected' : '' ?>>Plano Básico Mensal (60 produtos)</option>
                                <option value="Plano Destaque Local" <?= old('plano') === 'Plano Destaque Local' ? 'selected' : '' ?>>Plano Destaque Local</option>
                                <option value="Plano Super Destaque (com Linefast)" <?= old('plano') === 'Plano Super Destaque (com Linefast)' ? 'selected' : '' ?>>Plano Super Destaque (com Linefast)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg fw-bold rounded-pill w-100 shadow-sm py-3">
                        <i class="fa-solid fa-paper-plane me-2"></i> Solicitar Ativação Online
                    </button>
                    <p class="text-center text-muted small mt-3 mb-0">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Seus dados estão protegidos. Entraremos em contato apenas para fins de ativação.
                    </p>
                </form>
            </div>
        </div>

        <!-- ATENDIMENTO DIRETO COM LOWEL TREVISAN -->
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-4 h-100">
                <!-- CARD CONSULTOR COMERCIAL -->
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 bg-white">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 24px;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded-pill">ATENDIMENTO DIRETO</span>
                            <h5 class="fw-bold text-dark mb-0">Lowel Trevisan</h5>
                            <small class="text-muted">Consultor Comercial Boca Santa</small>
                        </div>
                    </div>

                    <p class="text-secondary small mb-3">
                        Prefere um atendimento imediato ou quer tirar dúvidas antes de cadastrar? Fale direto com nosso consultor comercial via WhatsApp ou ligação:
                    </p>

                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small text-muted"><i class="fa-solid fa-phone me-2 text-danger"></i>Telefone / WhatsApp:</span>
                            <strong class="text-dark fs-6">(19) 9.9769.66.60</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small text-muted"><i class="fa-solid fa-envelope me-2 text-danger"></i>E-mail:</span>
                            <strong class="text-dark small">vendas@bocasantaofertas.com.br</strong>
                        </div>
                    </div>

                    <a href="https://api.whatsapp.com/send?phone=5519997696660&text=Ol%C3%A1%20Lowel%2C%20gostaria%20de%20anunciar%20minha%20empresa%20no%20Boca%20Santa%20Ofertas." target="_blank" class="btn btn-success btn-lg fw-bold rounded-pill w-100 shadow-sm py-3 text-white">
                        <i class="fa-brands fa-whatsapp fs-5 me-2"></i> Falar com Lowel no WhatsApp
                    </a>
                </div>

                <!-- CARD COMO FUNCIONA -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white flex-grow-1">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-circle-question text-primary me-2"></i> Como funciona após o envio?</h5>
                    <ol class="small text-secondary ps-3 mb-0 lh-lg">
                        <li class="mb-2"><strong>Recebimento:</strong> Sua proposta é enviada imediatamente para nossa equipe comercial.</li>
                        <li class="mb-2"><strong>Ativação:</strong> O consultor <strong>Lowel Trevisan</strong> entra em contato para alinhar as primeiras ofertas e fotos.</li>
                        <li><strong>Visibilidade:</strong> Sua loja entra no ar no portal e no Google para atrair clientes de Piracicaba e região!</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function selecionarPlano(nome) {
    var select = document.getElementById('selectPlano');
    if (select) {
        select.value = nome;
    }
}
</script>
<?= $this->endSection() ?>
