<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= base_url($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="text-decoration-none text-muted">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/categoria/' . ($oferta->cat_url ?? 'geral')) ?>" class="text-decoration-none text-muted"><?= esc($oferta->cat_titulo ?? 'Categoria') ?></a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 300px;" aria-current="page"><?= esc($oferta->pro_titulo) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- COLUNA ESQUERDA: IMAGEM, DESCRIÇÃO, DADOS DO PARCEIRO & MAPA -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="position-relative bg-light text-center">
                    <img src="<?= $oferta->getImagemUrl() ?>" alt="<?= esc($oferta->pro_titulo) ?>" class="img-fluid rounded-top" style="max-height: 480px; width: 100%; object-fit: contain; background: #f8fafc;" onerror="this.src='<?= base_url('assets/images/sem_foto.gif') ?>'">
                    <?php if ($oferta->getPercentualDesconto() > 0): ?>
                        <span class="position-absolute top-0 start-0 m-3 badge bg-danger fs-6 px-3 py-2 rounded-pill shadow">
                            -<?= $oferta->getPercentualDesconto() ?>% DE DESCONTO
                        </span>
                    <?php endif; ?>
                    <?php if ($oferta->isLinefast()): ?>
                        <span class="position-absolute top-0 end-0 m-3 badge bg-primary fs-6 px-3 py-2 rounded-pill shadow">
                            <i class="fa-solid fa-bolt me-1"></i> COMPRA ONLINE LINEFAST
                        </span>
                    <?php endif; ?>
                </div>

                <div class="card-body p-4 p-md-5">
                    <h1 class="h2 fw-bold text-dark mb-3"><?= esc($oferta->pro_titulo) ?></h1>

                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small pb-4 border-bottom mb-4">
                        <span><i class="fa-solid fa-eye me-1"></i> <?= number_format($oferta->pro_visitas ?? 0, 0, ',', '.') ?> visualizações</span>
                        <span><i class="fa-solid fa-location-dot me-1 text-danger"></i> <?= esc($oferta->cid_nome ?? 'Piracicaba') ?> - SP</span>
                        <span><i class="fa-solid fa-store me-1"></i> <?= esc($oferta->par_nome ?? 'Parceiro') ?></span>
                    </div>

                    <!-- DESCRIÇÃO DA OFERTA -->
                    <div class="mb-4">
                        <h4 class="fw-bold mb-3"><i class="fa-solid fa-circle-info text-danger me-2"></i> Descrição da Oferta</h4>
                        <div class="text-secondary lh-lg fs-6">
                            <?= nl2br(esc($oferta->pro_descricao ?? '')) ?>
                        </div>
                    </div>

                    <?php if (!empty($oferta->pro_caracteristicas)): ?>
                    <div class="mb-4">
                        <h4 class="fw-bold mb-3"><i class="fa-solid fa-list-check text-danger me-2"></i> Características & Especificações</h4>
                        <div class="text-secondary lh-lg">
                            <?= nl2br(esc($oferta->pro_caracteristicas)) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <hr class="my-5">

                    <!-- DADOS COMPLETOS DE CONTATO DO PARCEIRO (ITEM 5) -->
                    <div class="mb-4">
                        <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-address-card text-danger me-2"></i> Dados de Contato do Estabelecimento</h4>
                        
                        <div class="card border-0 bg-light rounded-4 p-4 mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="fs-4 text-danger"><i class="fa-solid fa-location-dot"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">Endereço:</strong>
                                            <span class="text-muted small">
                                                <?= esc($oferta->par_endereco ?? '') ?>, <?= esc($oferta->par_numero ?? 'S/N') ?>
                                                <?php if (!empty($oferta->par_complemento)): ?> - <?= esc($oferta->par_complemento) ?><?php endif; ?>
                                                <br><?= esc($oferta->par_bairro ?? '') ?> &bull; <?= esc($oferta->cid_nome ?? 'Piracicaba') ?> - SP
                                                <?php if (!empty($oferta->par_cep)): ?><br>CEP: <?= esc($oferta->par_cep) ?><?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="fs-4 text-success"><i class="fa-brands fa-whatsapp"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">Telefones & Atendimento:</strong>
                                            <span class="text-muted small">
                                                <?php if (!empty($oferta->par_telefone)): ?><i class="fa-solid fa-phone me-1"></i> <?= esc($oferta->par_telefone) ?><br><?php endif; ?>
                                                <?php if (!empty($oferta->par_telefone2)): ?><i class="fa-solid fa-phone me-1"></i> <?= esc($oferta->par_telefone2) ?><br><?php endif; ?>
                                                <?php if (!empty($oferta->par_whatsapp)): ?><i class="fa-brands fa-whatsapp text-success me-1"></i> <?= esc($oferta->par_whatsapp) ?><?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($oferta->par_email)): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="fs-4 text-primary"><i class="fa-solid fa-envelope"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">E-mail:</strong>
                                            <a href="mailto:<?= esc($oferta->par_email) ?>" class="text-muted small text-decoration-none"><?= esc($oferta->par_email) ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($oferta->par_site)): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="fs-4 text-info"><i class="fa-solid fa-globe"></i></div>
                                        <div>
                                            <strong class="d-block text-dark">Website / Redes:</strong>
                                            <a href="<?= esc($oferta->par_site) ?>" target="_blank" class="text-muted small text-decoration-none"><?= esc($oferta->par_site) ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($oferta->par_descricao_empresa)): ?>
                                <hr class="my-3">
                                <div class="small text-secondary">
                                    <strong class="text-dark d-block mb-1">Sobre o Parceiro:</strong>
                                    <?= nl2br(esc($oferta->par_descricao_empresa)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- MAPA DE LOCALIZAÇÃO DO GOOGLE MAPS (ITEM 1) -->
                    <div class="mb-4">
                        <h4 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-map-location-dot text-danger me-2"></i> Localização no Mapa</h4>
                        <div class="ratio ratio-16x9 rounded-4 overflow-hidden border shadow-sm">
                            <iframe 
                                src="<?= $parceiro ? $parceiro->getGoogleMapsEmbedUrl($oferta->cid_nome ?? 'Piracicaba') : 'https://maps.google.com/maps?q=' . urlencode($oferta->cid_nome ?? 'Piracicaba') . '&t=&z=15&ie=UTF8&iwloc=&output=embed' ?>" 
                                width="100%" 
                                height="350" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>

                    <!-- REGULAMENTO -->
                    <div class="p-4 rounded-4 bg-light border">
                        <h5 class="fw-bold mb-2"><i class="fa-solid fa-shield-halved text-success me-2"></i> Regulamento & Utilização</h5>
                        <p class="small text-muted mb-0">
                            Apresente o cupom ou informe o anúncio do Boca Santa Ofertas diretamente no estabelecimento parceiro. Válido enquanto durarem os estoques. Para ofertas integradas ao Linefast, o processamento de pagamento e entrega é realizado com total segurança pela plataforma parceira.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUNA DIREITA: PREÇO, COMPRA / WHATSAPP, PARCEIRO & BANNER ANUNCIE -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white sticky-lg-top" style="top: 90px; z-index: 10;">
                <div class="mb-4">
                    <!-- PREÇO DE (ITEM 4) -->
                    <?php if ($oferta->hasPrecoDe()): ?>
                        <div class="text-muted text-decoration-line-through small">De: <?= $oferta->getPrecoOriginalFormatado() ?></div>
                    <?php endif; ?>

                    <!-- PREÇO ATUAL / CONSULTE O PREÇO (ITEM 2 & 6) -->
                    <div class="display-6 fw-extrabold text-danger mb-1">
                        <?= $oferta->getPrecoVendaFormatado() ?>
                    </div>

                    <!-- MELHOR PREÇO GARANTIDO (ITEM 3 - SÓ SE pro_melhor_preco == 1) -->
                    <?php if (!empty($oferta->pro_melhor_preco) && (int)$oferta->pro_melhor_preco === 1): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Melhor Preço Garantido</span>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 mb-4">
                    <?php if ($oferta->isLinefast()): ?>
                        <a href="<?= $oferta->getLinefastCartUrl() ?>" class="btn btn-primary btn-lg fw-bold rounded-pill py-3 shadow" target="_blank">
                            <i class="fa-solid fa-cart-shopping me-2"></i> Comprar no Linefast
                        </a>
                        <small class="text-center text-muted"><i class="fa-solid fa-lock text-success me-1"></i> Redirecionamento seguro para o carrinho Linefast</small>
                    <?php elseif (!$oferta->hasPreco()): ?>
                        <!-- CONSULTAR PREÇO NO WHATSAPP (ITEM 2) -->
                        <a href="<?= $oferta->getWhatsappConsultaLink($oferta->par_whatsapp ?? $oferta->par_telefone ?? '') ?>" class="btn btn-success btn-lg fw-bold rounded-pill py-3 shadow" target="_blank">
                            <i class="fa-brands fa-whatsapp me-2 fs-5"></i> Consultar Preço no WhatsApp
                        </a>
                        <small class="text-center text-muted">Fale diretamente com o anunciante pelo WhatsApp</small>
                    <?php else: ?>
                        <a href="<?= $oferta->getWhatsappConsultaLink($oferta->par_whatsapp ?? $oferta->par_telefone ?? '') ?>" class="btn btn-success btn-lg fw-bold rounded-pill py-3 shadow" target="_blank">
                            <i class="fa-brands fa-whatsapp me-2 fs-5"></i> Garantir Desconto no WhatsApp
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($cartaoFidelidade)): ?>
                    <!-- CARD PROGRAMA FIDELIDADE -->
                    <div class="card border-0 rounded-4 p-3 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill"><i class="fa-solid fa-star me-1"></i> PROGRAMA FIDELIDADE</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-white"><?= esc($cartaoFidelidade['car_nome']) ?></h6>
                        <p class="small text-white-50 mb-2">
                            A cada compra nesta empresa você acumula pontos para resgatar recompensas exclusivas!
                        </p>
                        <div class="bg-white bg-opacity-10 p-2 rounded-3 small">
                            <div class="d-flex justify-content-between text-white fw-semibold mb-1">
                                <span><i class="fa-solid fa-bullseye me-1"></i> Meta do Cartão:</span>
                                <span><?= $cartaoFidelidade['car_pontos'] ?> Pontos / Carimbos</span>
                            </div>
                            <?php if (!empty($cartaoFidelidade['car_regras'])): ?>
                                <div class="text-white-50 small mt-1">
                                    <i class="fa-solid fa-gift me-1 text-warning"></i> <?= esc($cartaoFidelidade['car_regras']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <!-- CARD RESUMIDO DO PARCEIRO -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="<?= $parceiro ? $parceiro->getLogoUrl() : base_url('assets/images/logo.png') ?>" alt="<?= esc($oferta->par_nome) ?>" style="max-height: 60px; max-width: 90px; width: auto; height: auto; object-fit: contain;">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><?= esc($oferta->par_nome) ?></h6>
                        <small class="text-muted"><i class="fa-solid fa-check text-success me-1"></i> Parceiro Verificado</small>
                    </div>
                </div>

                <a href="<?= base_url(($cidadeAtual['cid_url'] ?? 'piracicaba') . '/parceiro/' . ($parceiro ? $parceiro->getSlug() : $oferta->pro_parceiro)) ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold w-100 mb-4">
                    Ver todas as ofertas deste parceiro
                </a>

                <!-- BANNER LATERAL: ANUNCIE JÁ! CLIQUE AQUI (ITEM 13) -->
                <div class="card border-0 rounded-4 p-4 text-center text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h5 class="fw-extrabold mb-1">Anuncie já!</h5>
                    <p class="small text-dark fw-bold mb-3">É fácil, rápido e barato!</p>
                    <a href="<?= base_url('anuncie') ?>" class="btn btn-dark btn-sm rounded-pill fw-bold shadow-sm">
                        Clique aqui e turbine suas vendas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- OFERTAS RELACIONADAS -->
    <?php if (!empty($relacionadas)): ?>
    <div class="mt-5">
        <h3 class="section-title mb-4">Ofertas que você também pode gostar</h3>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php foreach ($relacionadas as $rel): ?>
            <div class="col">
                <div class="offer-card">
                    <div class="offer-card-media">
                        <a href="<?= $rel->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>">
                            <img src="<?= $rel->getImagemUrl() ?>" alt="<?= esc($rel->pro_titulo) ?>" loading="lazy">
                        </a>
                    </div>
                    <div class="offer-card-body">
                        <span class="offer-partner-name"><?= esc($rel->par_nome ?? 'Parceiro') ?></span>
                        <a href="<?= $rel->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="offer-card-title"><?= esc($rel->pro_titulo) ?></a>
                        <div class="offer-price-box">
                            <div class="price-current <?= !$rel->hasPreco() ? 'fs-6 text-muted' : '' ?>"><?= $rel->getPrecoVendaFormatado() ?></div>
                            <a href="<?= $rel->getUrl($cidadeAtual['cid_url'] ?? 'piracicaba') ?>" class="btn-offer-action">Ver</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
