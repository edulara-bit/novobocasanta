<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($oferta) ? base_url('admin/ofertas/atualizar/' . $oferta->pro_id) : base_url('admin/ofertas/salvar') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 form-group">
                            <label for="pro_titulo">Título da Oferta / Produto <span class="text-danger">*</span></label>
                            <input type="text" name="pro_titulo" id="pro_titulo" class="form-control" value="<?= esc($oferta->pro_titulo ?? old('pro_titulo') ?? '') ?>" required>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="pro_parceiro">Parceiro Anunciante <span class="text-danger">*</span></label>
                            <select name="pro_parceiro" id="pro_parceiro" class="form-control" required>
                                <option value="">Selecione o parceiro...</option>
                                <?php foreach ($parceiros as $p): ?>
                                    <option value="<?= $p->par_id ?>" <?= (!empty($oferta) && (int)$oferta->pro_parceiro === (int)$p->par_id) ? 'selected' : '' ?>>
                                        <?= esc($p->getNome()) ?> (<?= esc($p->cid_nome ?? '') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="pro_categoria">Categoria</label>
                            <select name="pro_categoria" id="pro_categoria" class="form-control">
                                <option value="0">Selecione uma categoria...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat->cat_id ?>" <?= (!empty($oferta) && (int)$oferta->pro_categoria === (int)$cat->cat_id) ? 'selected' : '' ?>>
                                        <?= esc($cat->cat_titulo) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="pro_cidade">Cidade da Oferta</label>
                            <select name="pro_cidade" id="pro_cidade" class="form-control">
                                <?php foreach ($cidades as $c): ?>
                                    <option value="<?= $c['cid_id'] ?>" <?= (!empty($oferta) && (int)$oferta->pro_cidade === (int)$c['cid_id']) ? 'selected' : '' ?>>
                                        <?= esc($c['cid_nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="pro_preco">Preço "De" (R$)</label>
                            <input type="text" name="pro_preco" id="pro_preco" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)$oferta->pro_preco, 2, ',', '.') : '' ?>">
                            <small class="text-muted">Deixe 0,00 se não houver.</small>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="pro_precodesconto">Preço "Por" (R$)</label>
                            <input type="text" name="pro_precodesconto" id="pro_precodesconto" class="form-control" placeholder="0,00" value="<?= !empty($oferta) ? number_format((float)$oferta->pro_precodesconto, 2, ',', '.') : '' ?>">
                            <small class="text-muted">0,00 = Consulte WhatsApp.</small>
                        </div>
                    </div>

                    <!-- OPÇÃO: MELHOR PREÇO GARANTIDO (DEFAULT NÃO EXIBIR) -->
                    <div class="row my-2">
                        <div class="col-md-6">
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" name="pro_melhor_preco" class="custom-control-input" id="pro_melhor_preco" value="1" <?= (!empty($oferta->pro_melhor_preco) && (int)$oferta->pro_melhor_preco === 1) ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold" for="pro_melhor_preco">
                                    <i class="fas fa-shield-alt text-success mr-1"></i> Exibir selo "Melhor Preço Garantido"
                                </label>
                            </div>
                            <small class="text-muted d-block">Padrão: Não exibir. Ative apenas quando o parceiro garantir paridade de preço.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" name="pro_destaque" class="custom-control-input" id="pro_destaque" value="1" <?= (!empty($oferta->pro_destaque) && (int)$oferta->pro_destaque === 1) ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold" for="pro_destaque">
                                    <i class="fas fa-fire text-danger mr-1"></i> Destacar na Home do Portal
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="foto">Foto Principal do Produto / Oferta</label>
                        <input type="file" name="foto" id="foto" class="form-control-file">
                        <?php if (!empty($oferta) && $oferta->getImagemUrl()): ?>
                            <div class="mt-2">
                                <img src="<?= $oferta->getImagemUrl() ?>" alt="" style="max-height: 100px; border-radius: 6px;" class="border p-1">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="pro_descricao">Descrição Detalhada da Oferta</label>
                        <textarea name="pro_descricao" id="pro_descricao" rows="4" class="form-control"><?= esc($oferta->pro_descricao ?? old('pro_descricao') ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="pro_caracteristicas">Características / Regulamento</label>
                        <textarea name="pro_caracteristicas" id="pro_caracteristicas" rows="3" class="form-control"><?= esc($oferta->pro_caracteristicas ?? old('pro_caracteristicas') ?? '') ?></textarea>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/ofertas') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> Salvar Oferta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
