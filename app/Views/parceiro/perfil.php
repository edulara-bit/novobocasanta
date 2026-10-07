<?= $this->extend('parceiro/layouts/partner') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <h3 class="fw-bold text-dark mb-4"><i class="fa-solid fa-building text-danger me-2"></i> Meus Dados & Configurações da Empresa</h3>

            <form action="<?= base_url('parceiro/perfil/salvar') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <h5 class="fw-bold text-secondary mb-3">1. Dados Comerciais</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nome Fantasia / Comercial <span class="text-danger">*</span></label>
                        <input type="text" name="par_nome" class="form-control" value="<?= esc($parceiro->par_nome ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Razão Social</label>
                        <input type="text" name="par_razao" class="form-control" value="<?= esc($parceiro->par_razao ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CNPJ</label>
                        <input type="text" name="par_cnpj" class="form-control" value="<?= esc($parceiro->par_cnpj ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">E-mail Principal de Contato</label>
                        <input type="email" name="par_email" class="form-control" value="<?= esc($parceiro->par_email ?? '') ?>">
                    </div>
                </div>

                <h5 class="fw-bold text-secondary mb-3">2. Endereço & Localização</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Cidade</label>
                        <select name="par_cidade" class="form-select">
                            <?php foreach ($cidades as $c): ?>
                                <option value="<?= $c['cid_id'] ?>" <?= ((int)$parceiro->par_cidade === (int)$c['cid_id']) ? 'selected' : '' ?>>
                                    <?= esc($c['cid_nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="par_estado" class="form-select">
                            <?php foreach ($estados as $e): ?>
                                <option value="<?= $e['est_id'] ?>" <?= ((int)$parceiro->par_estado === (int)$e['est_id']) ? 'selected' : '' ?>>
                                    <?= esc($e['est_nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">CEP</label>
                        <input type="text" name="par_cep" class="form-control" value="<?= esc($parceiro->par_cep ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Endereço (Rua/Av.)</label>
                        <input type="text" name="par_endereco" class="form-control" value="<?= esc($parceiro->par_endereco ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Número</label>
                        <input type="text" name="par_numero" class="form-control" value="<?= esc($parceiro->par_numero ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bairro</label>
                        <input type="text" name="par_bairro" class="form-control" value="<?= esc($parceiro->par_bairro ?? '') ?>">
                    </div>
                </div>

                <h5 class="fw-bold text-secondary mb-3">3. Atendimento & Contatos</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">WhatsApp Comercial (com DDD)</label>
                        <input type="text" name="par_whatsapp" class="form-control" value="<?= esc($parceiro->par_whatsapp ?? '') ?>" placeholder="(19) 99999-9999">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Telefone Fixo 1</label>
                        <input type="text" name="par_telefone" class="form-control" value="<?= esc($parceiro->par_telefone ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Telefone Fixo 2</label>
                        <input type="text" name="par_telefone2" class="form-control" value="<?= esc($parceiro->par_telefone2 ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Website</label>
                        <input type="text" name="par_site" class="form-control" value="<?= esc($parceiro->par_site ?? '') ?>" placeholder="https://...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Instagram</label>
                        <input type="text" name="par_instagram" class="form-control" value="<?= esc($parceiro->par_instagram ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Facebook</label>
                        <input type="text" name="par_facebook" class="form-control" value="<?= esc($parceiro->par_facebook ?? '') ?>">
                    </div>
                </div>

                <h5 class="fw-bold text-secondary mb-3">4. Logomarca & Apresentação</h5>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Logomarca da Empresa</label>
                    <input type="file" name="par_imagem" class="form-control">
                    <?php if ($parceiro->getLogoUrl()): ?>
                        <div class="mt-2">
                            <img src="<?= $parceiro->getLogoUrl() ?>" alt="" style="max-height: 90px; object-fit: contain;" class="border p-2 rounded-3 bg-light">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Descrição / Sobre a Empresa</label>
                    <textarea name="par_descricao" rows="4" class="form-control"><?= esc($parceiro->par_descricao ?? '') ?></textarea>
                </div>

                <h5 class="fw-bold text-secondary mb-3">5. Segurança & Senha</h5>
                <div class="mb-4 col-md-6">
                    <label class="form-label fw-semibold">Alterar Senha de Acesso</label>
                    <input type="password" name="par_senha" class="form-control" placeholder="Deixe em branco para manter a senha atual">
                </div>

                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="<?= base_url('parceiro/dashboard') ?>" class="btn btn-secondary rounded-pill px-4">Voltar</a>
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold px-5">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
