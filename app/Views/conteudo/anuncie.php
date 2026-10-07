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
                <a href="#planosAnuncio" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 text-dark shadow-sm">
                    Ver Planos & Contratação Online
                </a>
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

    <!-- PLANOS E FORMULÁRIO -->
    <div class="row g-4" id="planosAnuncio">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                <h3 class="fw-bold text-dark mb-3"><i class="fa-solid fa-store text-danger me-2"></i> Cadastre sua Empresa</h3>
                <p class="text-muted small mb-4">Preencha os dados abaixo para nossa equipe ativar sua conta e cadastrar suas ofertas:</p>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success rounded-3 mb-4">
                        <i class="fa-solid fa-circle-check me-2"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger rounded-3 mb-4">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('anuncie/enviar') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do Responsável <span class="text-danger">*</span></label>
                        <input type="text" name="nome" class="form-control rounded-3" placeholder="Seu nome" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome da Empresa / Comércio <span class="text-danger">*</span></label>
                        <input type="text" name="empresa" class="form-control rounded-3" placeholder="Nome Fantasia" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="contato@empresa.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">WhatsApp / Telefone <span class="text-danger">*</span></label>
                            <input type="text" name="telefone" class="form-control rounded-3" placeholder="(19) 99999-9999" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cidade <span class="text-danger">*</span></label>
                            <input type="text" name="cidade" class="form-control rounded-3" value="<?= esc($cidadeAtual['cid_nome'] ?? 'Piracicaba') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Plano de Interesse</label>
                            <select name="plano" class="form-select rounded-3">
                                <option value="Super Destaque">Super Destaque (Mais Vendido)</option>
                                <option value="Destaque">Destaque</option>
                                <option value="Básico">Básico</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg fw-bold rounded-pill w-100 shadow-sm py-3">
                        <i class="fa-solid fa-paper-plane me-2"></i> Solicitar Ativação Online
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <span class="badge bg-danger-subtle text-danger align-self-start px-3 py-1 rounded-pill fw-bold mb-2">PLANO DESTAQUE</span>
                        <h4 class="fw-bold mb-1">Destaque Local</h4>
                        <div class="display-6 fw-extrabold text-dark my-3">R$ 99<small class="fs-6 text-muted">/mês</small></div>
                        <ul class="list-unstyled small text-secondary lh-lg mb-4">
                            <li><i class="fa-solid fa-check text-success me-2"></i> Até 10 ofertas ativas simultâneas</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Perfil institucional com fotos e mapas</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Botão direto para WhatsApp</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Indexação SEO no Google</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-2 border-danger shadow rounded-4 p-4 bg-white h-100 position-relative">
                        <span class="position-absolute top-0 end-0 m-3 badge bg-danger px-3 py-1 rounded-pill fw-bold">MAIS POPULAR</span>
                        <span class="badge bg-primary-subtle text-primary align-self-start px-3 py-1 rounded-pill fw-bold mb-2">COM LINEFAST</span>
                        <h4 class="fw-bold mb-1">Super Destaque</h4>
                        <div class="display-6 fw-extrabold text-danger my-3">R$ 179<small class="fs-6 text-muted">/mês</small></div>
                        <ul class="list-unstyled small text-secondary lh-lg mb-4">
                            <li><i class="fa-solid fa-check text-success me-2"></i> Ofertas ilimitadas</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Posição prioritária na Home e Categorias</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> <strong>Sincronização com Linefast</strong></li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Deep link de compras no carrinho</li>
                            <li><i class="fa-solid fa-check text-success me-2"></i> Suporte prioritário</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
