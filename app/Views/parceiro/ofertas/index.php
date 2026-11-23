<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-tags text-danger me-2"></i> Minhas Ofertas & Produtos
                <?php if ($parceiro->hasOfertasIlimitadas()): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 ms-2">
                        <i class="fa-solid fa-bolt me-1"></i> <?= (int)($totalOfertas ?? count($ofertas)) ?> ofertas (Ilimitadas - Linefast)
                    </span>
                <?php else: ?>
                    <span class="badge bg-light text-secondary border fs-6 ms-2">
                        <?= (int)($totalOfertas ?? count($ofertas)) ?> / 60 ofertas
                    </span>
                <?php endif; ?>
            </h3>
            <p class="text-muted small mb-0">Cadastre e gerencie as ofertas da sua empresa exibidas no portal Boca Santa.</p>
        </div>
        <a href="<?= base_url('parceiro/ofertas/criar') ?>" class="btn btn-danger rounded-pill fw-bold px-4">
            <i class="fa-solid fa-plus-circle me-1"></i> Nova Oferta
        </a>
    </div>

    <!-- BARRA DE BUSCA DE OFERTAS -->
    <form action="<?= base_url('parceiro/ofertas') ?>" method="get" class="row g-2 align-items-center mb-4">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </span>
                <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Buscar por título ou descrição da oferta..." value="<?= esc($busca ?? '') ?>">
                <button type="submit" class="btn btn-dark rounded-end-pill px-4 fw-semibold">Buscar</button>
            </div>
        </div>
        <?php if (!empty($busca)): ?>
            <div class="col-auto">
                <a href="<?= base_url('parceiro/ofertas') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-xmark me-1"></i> Limpar filtro
                </a>
            </div>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Foto</th>
                    <th>Título</th>
                    <th>Preço "De"</th>
                    <th>Preço "Por"</th>
                    <th>Melhor Preço</th>
                    <th>Visualizações</th>
                    <th class="text-end" style="width: 170px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ofertas)): ?>
                    <?php foreach ($ofertas as $o): ?>
                        <tr>
                            <td>
                                <img src="<?= $o->getImagemUrl() ?>" alt="" class="rounded-3" width="60" height="45" style="object-fit: cover;">
                            </td>
                            <td>
                                <strong><?= esc($o->pro_titulo) ?></strong>
                                <?php if ($o->isLinefast()): ?>
                                    <span class="badge bg-primary ms-1"><i class="fa-solid fa-bolt"></i> Linefast</span>
                                <?php endif; ?>
                                <?php if ($o->hasExternalBuyLink()): ?>
                                    <span class="badge bg-warning text-dark ms-1"><i class="fa-solid fa-bag-shopping"></i> <?= $o->isMercadoLivre() ? 'Mercado Livre' : 'Loja Online' ?></span>
                                <?php endif; ?>
                                <?php if (!empty($o->pro_video)): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" title="Possui vídeo do YouTube"><i class="fa-brands fa-youtube"></i> Vídeo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small text-decoration-line-through">
                                <?= $o->hasPrecoDe() ? $o->getPrecoOriginalFormatado() : '-' ?>
                            </td>
                            <td class="text-danger fw-bold">
                                <?= $o->getPrecoVendaFormatado() ?>
                            </td>
                            <td>
                                <?php if (!empty($o->pro_melhor_preco) && (int)$o->pro_melhor_preco === 1): ?>
                                    <span class="badge bg-success-subtle text-success border">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Não</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-eye me-1"></i> <?= number_format($o->pro_visitas ?? 0, 0, ',', '.') ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= $o->getUrl() ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Ver no site">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <a href="<?= base_url('parceiro/ofertas/editar/' . $o->pro_id) ?>" class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="<?= base_url('parceiro/ofertas/excluir/' . $o->pro_id) ?>" class="btn btn-sm btn-outline-danger rounded-pill" title="Excluir" onclick="return confirm('Deseja excluir esta oferta?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-tags fs-1 d-block mb-3 text-muted"></i>
                            <?php if (!empty($busca)): ?>
                                Nenhuma oferta encontrada para a busca "<strong><?= esc($busca) ?></strong>".<br>
                                <a href="<?= base_url('parceiro/ofertas') ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold mt-2">Ver Todas as Ofertas</a>
                            <?php else: ?>
                                Você ainda não possui ofertas cadastradas.<br>
                                <a href="<?= base_url('parceiro/ofertas/criar') ?>" class="btn btn-danger btn-sm rounded-pill fw-bold mt-2">Criar Minha Primeira Oferta</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINAÇÃO -->
    <?php
        $pagAtual = (int)($paginaAtual ?? 1);
        $totPag   = (int)($totalPaginas ?? 1);
        $qParam   = !empty($busca) ? '&q=' . urlencode($busca) : '';
    ?>
    <?php if ($totPag > 1): ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 pt-3 border-top mt-2">
            <div class="small text-muted">
                Página <strong><?= $pagAtual ?></strong> de <strong><?= $totPag ?></strong> (Total: <?= (int)($totalOfertas ?? 0) ?> ofertas)
            </div>
            <nav aria-label="Paginação de ofertas">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $pagAtual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link rounded-start-pill px-3" href="<?= base_url('parceiro/ofertas?page=' . ($pagAtual - 1) . $qParam) ?>">
                            <i class="fa-solid fa-chevron-left small me-1"></i> Anterior
                        </a>
                    </li>
                    <?php
                        $inicio = max(1, $pagAtual - 2);
                        $fim    = min($totPag, $pagAtual + 2);
                        if ($inicio > 1):
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('parceiro/ofertas?page=1' . $qParam) ?>">1</a>
                        </li>
                        <?php if ($inicio > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($p = $inicio; $p <= $fim; $p++): ?>
                        <li class="page-item <?= $p === $pagAtual ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url('parceiro/ofertas?page=' . $p . $qParam) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($fim < $totPag): ?>
                        <?php if ($fim < $totPag - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('parceiro/ofertas?page=' . $totPag . $qParam) ?>"><?= $totPag ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item <?= $pagAtual >= $totPag ? 'disabled' : '' ?>">
                        <a class="page-link rounded-end-pill px-3" href="<?= base_url('parceiro/ofertas?page=' . ($pagAtual + 1) . $qParam) ?>">
                            Próxima <i class="fa-solid fa-chevron-right small ms-1"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
