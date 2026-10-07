<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <h3 class="fw-bold text-dark mb-4"><i class="fa-solid fa-tag text-danger me-2"></i> <?= esc($title) ?></h3>

            <form action="<?= !empty($oferta) ? base_url('parceiro/ofertas/atualizar/' . $oferta->pro_id) : base_url('parceiro/ofertas/salvar') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Título da Oferta / Nome do Produto <span class="text-danger">*</span></label>
                    <input type="text" name="pro_titulo" class="form-control" value="<?= esc($oferta->pro_titulo ?? old('pro_titulo') ?? '') ?>" placeholder="Ex: Marmitex Executiva, Troca de Óleo, Consultoria..." required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select name="pro_categoria" class="form-select">
                            <option value="0">Selecione uma categoria...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->cat_id ?>" <?= (!empty($oferta) && (int)$oferta->pro_categoria === (int)$cat->cat_id) ? 'selected' : '' ?>>
                                    <?= esc($cat->cat_titulo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Preço "De" (R$)</label>
                        <input type="text" name="pro_preco" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)$oferta->pro_preco, 2, ',', '.') : '' ?>">
                        <small class="text-muted">Deixe 0,00 para não exibir.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Preço "Por" (R$)</label>
                        <input type="text" name="pro_precodesconto" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)$oferta->pro_precodesconto, 2, ',', '.') : '' ?>">
                        <small class="text-muted">0,00 = Consulte WhatsApp.</small>
                    </div>
                </div>

                <!-- VINCULAR A UM CARTÃO FIDELIDADE -->
                <div class="card p-3 rounded-4 bg-light border my-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-dark mb-0">
                            <i class="fa-solid fa-id-card text-danger me-1"></i> Cartão Fidelidade da Oferta
                        </label>
                        <a href="<?= base_url('parceiro/fidelidade') ?>" target="_blank" class="small text-danger fw-semibold text-decoration-none">
                            <i class="fa-solid fa-gear me-1"></i> Gerenciar Cartões
                        </a>
                    </div>
                    <select name="pro_cartao" class="form-select mb-2">
                        <option value="0">Nenhum cartão vinculado (Oferta avulsa)</option>
                        <?php if (!empty($cartoes)): ?>
                            <?php foreach ($cartoes as $cart): ?>
                                <option value="<?= $cart['car_id'] ?>" <?= (!empty($oferta->pro_cartao) && (int)$oferta->pro_cartao === (int)$cart['car_id']) ? 'selected' : '' ?>>
                                    <?= esc($cart['car_nome']) ?> (Meta: <?= $cart['car_pontos'] ?> Pontos/Carimbos &bull; <?= $cart['car_validade'] ?> dias)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted">Ao selecionar um cartão, a oferta exibirá o selo e as vantagens do seu programa de fidelidade para os clientes pontuarem ao comprar.</small>
                </div>

                <!-- OPÇÃO MELHOR PREÇO GARANTIDO (DEFAULT: NÃO EXIBIR) -->
                <div class="card p-3 rounded-4 bg-light border my-4">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" name="pro_melhor_preco" id="pro_melhor_preco" value="1" <?= (!empty($oferta->pro_melhor_preco) && (int)$oferta->pro_melhor_preco === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-dark" for="pro_melhor_preco">
                            <i class="fa-solid fa-shield-halved text-success me-1"></i> Exibir selo "Melhor Preço Garantido"
                        </label>
                    </div>
                    <small class="text-muted">Por padrão esta opção fica desativada. Ative se sua empresa garante cobrir ofertas concorrentes ou garante o menor valor da praça.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto do Anúncio</label>
                    <input type="file" name="foto" class="form-control">
                    <?php if (!empty($oferta) && $oferta->getImagemUrl()): ?>
                        <div class="mt-2">
                            <img src="<?= $oferta->getImagemUrl() ?>" alt="" style="max-height: 120px; border-radius: 8px;" class="border p-1">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Descrição Detalhada</label>
                    <textarea name="pro_descricao" rows="4" class="form-control"><?= esc($oferta->pro_descricao ?? old('pro_descricao') ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Características & Regulamento</label>
                    <textarea name="pro_caracteristicas" rows="3" class="form-control"><?= esc($oferta->pro_caracteristicas ?? old('pro_caracteristicas') ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="<?= base_url('parceiro/ofertas') ?>" class="btn btn-secondary rounded-pill px-4">Voltar</a>
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold px-5">Salvar Oferta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
