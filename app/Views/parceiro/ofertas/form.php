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
                        <label class="form-label fw-semibold">Categoria Principal <span class="text-danger">*</span></label>
                        <select name="pro_categoria_pai" id="pro_categoria_pai" class="form-select" required>
                            <option value="">Selecione uma categoria principal...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat->cat_id ?>" <?= (isset($catPaiId) && (int)$catPaiId === (int)$cat->cat_id) ? 'selected' : '' ?>>
                                    <?= esc($cat->cat_titulo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Subcategoria <span class="text-muted fw-normal">(define a URL da oferta)</span></label>
                        <select name="pro_categoria" id="pro_categoria" class="form-select">
                            <?php if (!empty($subcategorias)): ?>
                                <option value="">Selecione uma subcategoria...</option>
                                <?php foreach ($subcategorias as $sub): ?>
                                    <option value="<?= $sub->cat_id ?>" <?= (isset($subCatId) && (int)$subCatId === (int)$sub->cat_id) ? 'selected' : '' ?>>
                                        <?= esc($sub->cat_titulo) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Selecione primeiro a categoria principal...</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted d-block mt-1">
                            <i class="fa-solid fa-circle-info text-danger me-1"></i> A subcategoria compõe a URL oficial no site (ex: <code>.../piracicaba/farmacia-de-manipulacao/...</code>).
                        </small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Preço "De" (R$)</label>
                        <input type="text" name="pro_preco" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)$oferta->pro_preco, 2, ',', '.') : '' ?>">
                        <small class="text-muted">Deixe 0,00 para não exibir.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Preço com Desconto "Por" (R$)</label>
                        <input type="text" name="pro_precodesconto" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)$oferta->pro_precodesconto, 2, ',', '.') : '' ?>">
                        <small class="text-muted">0,00 = Consulte WhatsApp.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">A partir de (R$)</label>
                        <input type="text" name="pro_apartirde" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)($oferta->pro_apartirde ?? 0), 2, ',', '.') : '' ?>">
                        <small class="text-muted">Se preenchido, exibe "A partir de R$".</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Marca do Produto</label>
                        <input type="text" name="pro_marca" class="form-control" placeholder="Ex:Marca / Fabricante (opcional)" value="<?= esc($oferta->pro_marca ?? old('pro_marca') ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Unidade</label>
                        <input type="text" name="pro_unidade" class="form-control" placeholder="Ex: Un, Kg, Caixa, Litro, Pç, etc." value="<?= esc($oferta->pro_unidade ?? old('pro_unidade') ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="fa-solid fa-bag-shopping text-warning me-1"></i> Link para Venda On-line (Mercado Livre / Loja Virtual)</label>
                        <input type="text" name="pro_comprar" class="form-control" placeholder="Copie e cole aqui o link para venda on-line (Mercado Livre, Loja, etc.)" value="<?= esc($oferta->pro_comprar ?? old('pro_comprar') ?? '') ?>">
                        <small class="text-muted">Exibe o botão de compra online / Mercado Livre na página da oferta.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><i class="fa-brands fa-youtube text-danger me-1"></i> Link de Vídeo (YouTube)</label>
                        <input type="text" name="pro_video" class="form-control" placeholder="Copie e cole aqui o link do vídeo no YouTube (se tiver)" value="<?= esc($oferta->pro_video ?? old('pro_video') ?? '') ?>">
                        <small class="text-muted">Exibe o botão "Assistir Vídeo" na página da oferta.</small>
                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const catPaiSelect = document.getElementById('pro_categoria_pai');
    const subCatSelect = document.getElementById('pro_categoria');
    const initialSubId = '<?= esc($subCatId ?? 0) ?>';

    function carregarSubcategorias(parentId, selectedSubId = null) {
        if (!parentId || parentId === '0') {
            subCatSelect.innerHTML = '<option value="">Selecione primeiro a categoria principal...</option>';
            return;
        }

        subCatSelect.innerHTML = '<option value="">Carregando subcategorias...</option>';
        subCatSelect.disabled = true;

        fetch('<?= base_url('api/categorias/subcategorias') ?>/' + encodeURIComponent(parentId))
            .then(res => res.json())
            .then(data => {
                subCatSelect.innerHTML = '';
                if (Array.isArray(data) && data.length > 0) {
                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = '';
                    defaultOpt.textContent = 'Selecione uma subcategoria...';
                    subCatSelect.appendChild(defaultOpt);

                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.cat_id;
                        opt.textContent = item.cat_titulo;
                        if (selectedSubId && String(item.cat_id) === String(selectedSubId)) {
                            opt.selected = true;
                        }
                        subCatSelect.appendChild(opt);
                    });
                } else {
                    const opt = document.createElement('option');
                    opt.value = parentId; // Fallback para a própria categoria pai
                    opt.textContent = '(Esta categoria não possui subcategorias cadastradas)';
                    opt.selected = true;
                    subCatSelect.appendChild(opt);
                }
                subCatSelect.disabled = false;
            })
            .catch(err => {
                console.error('Erro ao buscar subcategorias:', err);
                subCatSelect.innerHTML = '<option value="">Erro ao carregar subcategorias</option>';
                subCatSelect.disabled = false;
            });
    }

    if (catPaiSelect) {
        catPaiSelect.addEventListener('change', function () {
            carregarSubcategorias(this.value, null);
        });

        // Se já houver categoria principal selecionada e as subcategorias não vierem do servidor
        if (catPaiSelect.value && subCatSelect.options.length <= 1) {
            carregarSubcategorias(catPaiSelect.value, initialSubId);
        }
    }
});
</script>
<?= $this->endSection() ?>
