<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-flag mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($estado) ? base_url('admin/estados/atualizar/' . $estado['est_id']) : base_url('admin/estados/salvar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="est_nome">Nome do Estado / UF <span class="text-danger">*</span></label>
                        <input type="text" name="est_nome" id="est_nome" class="form-control" value="<?= esc($estado['est_nome'] ?? old('est_nome') ?? '') ?>" placeholder="ex: São Paulo ou SP" required>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/estados') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
