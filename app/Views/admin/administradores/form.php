<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-users-cog mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($admin) ? base_url('admin/administradores/atualizar/' . $admin['adm_id']) : base_url('admin/administradores/salvar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="adm_nome">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" name="adm_nome" id="adm_nome" class="form-control" value="<?= esc($admin['adm_nome'] ?? old('adm_nome') ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="adm_login">Login de Acesso <span class="text-danger">*</span></label>
                            <input type="text" name="adm_login" id="adm_login" class="form-control" value="<?= esc($admin['adm_login'] ?? old('adm_login') ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="adm_email">E-mail</label>
                            <input type="email" name="adm_email" id="adm_email" class="form-control" value="<?= esc($admin['adm_email'] ?? old('adm_email') ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="adm_senha">Senha de Acesso</label>
                            <input type="password" name="adm_senha" id="adm_senha" class="form-control" placeholder="<?= !empty($admin) ? 'Deixe em branco para não alterar' : 'Senha do administrador' ?>" <?= empty($admin) ? 'required' : '' ?>>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adm_status">Status da Conta</label>
                        <select name="adm_status" id="adm_status" class="form-control">
                            <option value="ativo" <?= (!empty($admin) && ($admin['adm_status'] ?? '') === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= (!empty($admin) && ($admin['adm_status'] ?? '') === 'inativo') ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/administradores') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Salvar Administrador</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
