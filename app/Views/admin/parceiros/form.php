<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-store mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($parceiro) ? base_url('admin/parceiros/atualizar/' . $parceiro->par_id) : base_url('admin/parceiros/salvar') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="par_nome">Nome Fantasia / Comercial <span class="text-danger">*</span></label>
                            <input type="text" name="par_nome" id="par_nome" class="form-control" value="<?= esc($parceiro->par_nome ?? old('par_nome') ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="par_razao">Razão Social</label>
                            <input type="text" name="par_razao" id="par_razao" class="form-control" value="<?= esc($parceiro->par_razao ?? old('par_razao') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="par_apelido">Slug / Apelido URL</label>
                            <input type="text" name="par_apelido" id="par_apelido" class="form-control" value="<?= esc($parceiro->par_apelido ?? old('par_apelido') ?? '') ?>" placeholder="ex: cabospira">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_email">E-mail de Contato / Login</label>
                            <input type="email" name="par_email" id="par_email" class="form-control" value="<?= esc($parceiro->par_email ?? old('par_email') ?? '') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_senha">Senha de Acesso ao Painel</label>
                            <input type="password" name="par_senha" id="par_senha" class="form-control" placeholder="<?= !empty($parceiro) ? 'Deixe em branco para manter' : 'Digite a senha' ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="par_cidade">Cidade</label>
                            <select name="par_cidade" id="par_cidade" class="form-control">
                                <?php foreach ($cidades as $c): ?>
                                    <option value="<?= $c['cid_id'] ?>" <?= (!empty($parceiro) && (int)$parceiro->par_cidade === (int)$c['cid_id']) ? 'selected' : '' ?>>
                                        <?= esc($c['cid_nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_estado">Estado</label>
                            <select name="par_estado" id="par_estado" class="form-control">
                                <?php foreach ($estados as $e): ?>
                                    <option value="<?= $e['est_id'] ?>" <?= (!empty($parceiro) && (int)$parceiro->par_estado === (int)$e['est_id']) ? 'selected' : '' ?>>
                                        <?= esc($e['est_nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_cep">CEP</label>
                            <input type="text" name="par_cep" id="par_cep" class="form-control" value="<?= esc($parceiro->par_cep ?? old('par_cep') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="par_endereco">Endereço (Rua/Avenida)</label>
                            <input type="text" name="par_endereco" id="par_endereco" class="form-control" value="<?= esc($parceiro->par_endereco ?? old('par_endereco') ?? '') ?>">
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="par_numero">Número</label>
                            <input type="text" name="par_numero" id="par_numero" class="form-control" value="<?= esc($parceiro->par_numero ?? old('par_numero') ?? '') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_bairro">Bairro</label>
                            <input type="text" name="par_bairro" id="par_bairro" class="form-control" value="<?= esc($parceiro->par_bairro ?? old('par_bairro') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="par_whatsapp">WhatsApp Comercial</label>
                            <input type="text" name="par_whatsapp" id="par_whatsapp" class="form-control" value="<?= esc($parceiro->par_whatsapp ?? old('par_whatsapp') ?? '') ?>" placeholder="(19) 99999-9999">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_telefone">Telefone Fixo 1</label>
                            <input type="text" name="par_telefone" id="par_telefone" class="form-control" value="<?= esc($parceiro->par_telefone ?? old('par_telefone') ?? '') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_telefone2">Telefone Fixo 2</label>
                            <input type="text" name="par_telefone2" id="par_telefone2" class="form-control" value="<?= esc($parceiro->par_telefone2 ?? old('par_telefone2') ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="par_site">Website Oficial</label>
                            <input type="text" name="par_site" id="par_site" class="form-control" value="<?= esc($parceiro->par_site ?? old('par_site') ?? '') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_facebook">Facebook (Link)</label>
                            <input type="text" name="par_facebook" id="par_facebook" class="form-control" value="<?= esc($parceiro->par_facebook ?? old('par_facebook') ?? '') ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="par_instagram">Instagram (Link)</label>
                            <input type="text" name="par_instagram" id="par_instagram" class="form-control" value="<?= esc($parceiro->par_instagram ?? old('par_instagram') ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="par_imagem">Logomarca da Empresa</label>
                        <input type="file" name="par_imagem" id="par_imagem" class="form-control-file">
                        <?php if (!empty($parceiro) && $parceiro->getLogoUrl()): ?>
                            <div class="mt-2">
                                <img src="<?= $parceiro->getLogoUrl() ?>" alt="" style="max-height: 80px; object-fit: contain;" class="border p-2 bg-light">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="par_descricao">Descrição da Empresa / Sobre</label>
                        <textarea name="par_descricao" id="par_descricao" rows="4" class="form-control"><?= esc($parceiro->par_descricao ?? old('par_descricao') ?? '') ?></textarea>
                    </div>

                    <div class="custom-control custom-switch mt-3">
                        <input type="checkbox" name="par_ativo" class="custom-control-input" id="par_ativo" value="1" <?= (!empty($parceiro) && $parceiro->par_ativo == '1') || empty($parceiro) ? 'checked' : '' ?>>
                        <label class="custom-control-label font-weight-bold" for="par_ativo">Parceiro Ativo no Portal</label>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/parceiros') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i> Salvar Parceiro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
