<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-image mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($banner) ? base_url('admin/banners/atualizar/' . $banner['ban_id']) : base_url('admin/banners/salvar') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">

                    <!-- SEÇÃO 1: TEXTOS & BOTÃO -->
                    <h5 class="font-weight-bold text-secondary border-bottom pb-2 mb-3">1. Conteúdo & Chamada do Banner</h5>
                    
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="ban_badge">Badge de Destaque</label>
                            <input type="text" name="ban_badge" id="ban_badge" class="form-control font-weight-bold" value="<?= esc($banner['ban_badge'] ?? old('ban_badge') ?? '⭐ CLUBE DE PARCEIROS') ?>" placeholder="ex: ⭐ SUPER DESCONTOS">
                        </div>
                        <div class="col-md-8 form-group">
                            <label for="ban_titulo">Título Principal <span class="text-danger">*</span></label>
                            <input type="text" name="ban_titulo" id="ban_titulo" class="form-control font-weight-bold" value="<?= esc($banner['ban_titulo'] ?? old('ban_titulo') ?? '') ?>" placeholder="ex: Conheça as melhores empresas da sua região" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ban_descricao">Texto Descritivo / Subtítulo</label>
                        <textarea name="ban_descricao" id="ban_descricao" rows="2" class="form-control" placeholder="ex: Comércios, lojas e prestadores de serviços de confiança com contato direto pelo WhatsApp."><?= esc($banner['ban_descricao'] ?? old('ban_descricao') ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="ban_botao_texto">Texto do Botão</label>
                            <input type="text" name="ban_botao_texto" id="ban_botao_texto" class="form-control" value="<?= esc($banner['ban_botao_texto'] ?? old('ban_botao_texto') ?? 'Conhecer Parceiros') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="ban_botao_link">Link de Destino do Botão</label>
                            <input type="text" name="ban_botao_link" id="ban_botao_link" class="form-control" value="<?= esc($banner['ban_botao_link'] ?? old('ban_botao_link') ?? 'parceiros') ?>" placeholder="ex: parceiros, anuncie ou categoria/alimentacao">
                        </div>
                    </div>

                    <!-- SEÇÃO 2: ESTILO DE FUNDO & IMAGENS -->
                    <h5 class="font-weight-bold text-secondary border-bottom pb-2 mb-3 mt-4">2. Estilo Visual de Fundo & Imagem de Destaque</h5>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="ban_tipo_fundo">Tipo de Fundo</label>
                            <select name="ban_tipo_fundo" id="ban_tipo_fundo" class="form-control" onchange="toggleTipoFundo(this.value)">
                                <option value="degrade" <?= (($banner['ban_tipo_fundo'] ?? '') === 'degrade' || empty($banner)) ? 'selected' : '' ?>>Degradê (Gradiente)</option>
                                <option value="cor" <?= (($banner['ban_tipo_fundo'] ?? '') === 'cor') ? 'selected' : '' ?>>Cor Única</option>
                                <option value="imagem" <?= (($banner['ban_tipo_fundo'] ?? '') === 'imagem') ? 'selected' : '' ?>>Imagem de Fundo</option>
                            </select>
                        </div>

                        <div class="col-md-8 form-group" id="group_fundo_degrade">
                            <label for="ban_fundo_degrade">Código do Gradiente CSS</label>
                            <input type="text" name="ban_fundo_degrade" id="ban_fundo_degrade" class="form-control font-weight-bold" value="<?= esc($banner['ban_fundo_degrade'] ?? old('ban_fundo_degrade') ?? 'linear-gradient(135deg, #059669 0%, #047857 100%)') ?>">
                            <div class="mt-2 d-flex flex-wrap gap-2">
                                <small class="text-muted mr-2">Presets rápidos:</small>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="document.getElementById('ban_fundo_degrade').value='linear-gradient(135deg, #059669 0%, #047857 100%)'; updateLivePreview();">Verde Esmeralda</button>
                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="document.getElementById('ban_fundo_degrade').value='linear-gradient(90deg, #991b1b 0%, #dc2626 50%, #ea580c 100%)'; updateLivePreview();">Vermelho Boca Santa</button>
                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="document.getElementById('ban_fundo_degrade').value='linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%)'; updateLivePreview();">Azul Linefast</button>
                                <button type="button" class="btn btn-xs btn-outline-dark" onclick="document.getElementById('ban_fundo_degrade').value='linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%)'; updateLivePreview();">Dark Noturno</button>
                            </div>
                        </div>

                        <div class="col-md-8 form-group d-none" id="group_fundo_cor">
                            <label for="ban_fundo_cor">Cor Única de Fundo</label>
                            <div class="input-group">
                                <input type="color" id="ban_color_picker" class="form-control col-2 p-1" value="<?= esc($banner['ban_fundo_cor'] ?? '#0f172a') ?>" onchange="document.getElementById('ban_fundo_cor').value=this.value; updateLivePreview();">
                                <input type="text" name="ban_fundo_cor" id="ban_fundo_cor" class="form-control" value="<?= esc($banner['ban_fundo_cor'] ?? old('ban_fundo_cor') ?? '#0f172a') ?>">
                            </div>
                        </div>

                        <div class="col-md-8 form-group d-none" id="group_fundo_imagem">
                            <label for="ban_fundo_imagem">Upload da Imagem de Fundo</label>
                            <input type="file" name="ban_fundo_imagem" id="ban_fundo_imagem" class="form-control-file">
                            <?php if (!empty($banner['ban_fundo_imagem'])): ?>
                                <?php $fundoSrc = str_starts_with($banner['ban_fundo_imagem'], 'http') ? $banner['ban_fundo_imagem'] : base_url($banner['ban_fundo_imagem']); ?>
                                <div class="mt-2">
                                    <img src="<?= esc($fundoSrc) ?>" alt="Fundo Atual" style="max-height: 80px;" class="border rounded p-1">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="ban_imagem_direita">Imagem de Destaque à Direita (PNG/JPG)</label>
                            <input type="file" name="ban_imagem_direita" id="ban_imagem_direita" class="form-control-file">
                            <?php if (!empty($banner['ban_imagem_direita'])): ?>
                                <?php $rightSrc = str_starts_with($banner['ban_imagem_direita'], 'http') ? $banner['ban_imagem_direita'] : base_url($banner['ban_imagem_direita']); ?>
                                <div class="mt-2">
                                    <img src="<?= esc($rightSrc) ?>" alt="Imagem Direita" style="max-height: 80px;" class="border rounded p-1 bg-light">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="ban_ordem">Ordem no Carrossel</label>
                            <input type="number" name="ban_ordem" id="ban_ordem" class="form-control" value="<?= esc($banner['ban_ordem'] ?? old('ban_ordem') ?? 1) ?>" min="1" max="99">
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="ban_status">Status</label>
                            <select name="ban_status" id="ban_status" class="form-control">
                                <option value="ativo" <?= (($banner['ban_status'] ?? 'ativo') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= (($banner['ban_status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        </div>
                    </div>

                    <!-- PREVIEW EM TEMPO REAL -->
                    <h5 class="font-weight-bold text-secondary border-bottom pb-2 mb-3 mt-4">3. Pré-visualização do Banner (Home)</h5>
                    <div id="bannerLivePreview" class="p-4 rounded-4 text-white shadow" style="min-height: 220px; background: linear-gradient(135deg, #059669 0%, #047857 100%); position: relative; overflow: hidden;">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="badge badge-warning text-dark font-weight-bold px-3 py-2 rounded-pill mb-2" id="prevBadge">⭐ CLUBE DE PARCEIROS</span>
                                <h3 class="font-weight-bold mb-2 text-white" id="prevTitle">Conheça as melhores empresas da sua região</h3>
                                <p class="text-white-50 small mb-3" id="prevDesc">Comércios, lojas e prestadores de serviços de confiança com contato direto pelo WhatsApp.</p>
                                <a href="#" class="btn btn-light btn-sm font-weight-bold rounded-pill px-4 text-dark shadow-sm" id="prevBtn">Conhecer Parceiros</a>
                            </div>
                            <div class="col-md-4 text-center d-none d-md-block">
                                <?php 
                                    $prevImg = base_url('assets/images/logo.png');
                                    if (!empty($banner['ban_imagem_direita'])) {
                                        $prevImg = str_starts_with($banner['ban_imagem_direita'], 'http') ? $banner['ban_imagem_direita'] : base_url($banner['ban_imagem_direita']);
                                    }
                                ?>
                                <img src="<?= esc($prevImg) ?>" id="prevRightImg" alt="" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/banners') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Salvar Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTipoFundo(tipo) {
    document.getElementById('group_fundo_degrade').classList.toggle('d-none', tipo !== 'degrade');
    document.getElementById('group_fundo_cor').classList.toggle('d-none', tipo !== 'cor');
    document.getElementById('group_fundo_imagem').classList.toggle('d-none', tipo !== 'imagem');
    updateLivePreview();
}

function updateLivePreview() {
    const title = document.getElementById('ban_titulo').value || 'Título do Banner';
    const badge = document.getElementById('ban_badge').value || '⭐ DESTAQUE';
    const desc = document.getElementById('ban_descricao').value || 'Descrição do banner publicitário.';
    const btn = document.getElementById('ban_botao_texto').value || 'Ver Mais';
    const tipo = document.getElementById('ban_tipo_fundo').value;
    
    document.getElementById('prevTitle').innerText = title;
    document.getElementById('prevBadge').innerText = badge;
    document.getElementById('prevDesc').innerText = desc;
    document.getElementById('prevBtn').innerText = btn;

    const preview = document.getElementById('bannerLivePreview');
    if (tipo === 'degrade') {
        preview.style.background = document.getElementById('ban_fundo_degrade').value;
    } else if (tipo === 'cor') {
        preview.style.background = document.getElementById('ban_fundo_cor').value;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    ['ban_titulo', 'ban_badge', 'ban_descricao', 'ban_botao_texto', 'ban_fundo_degrade', 'ban_fundo_cor'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updateLivePreview);
    });
    toggleTipoFundo(document.getElementById('ban_tipo_fundo').value);
    updateLivePreview();
});
</script>
<?= $this->endSection() ?>
