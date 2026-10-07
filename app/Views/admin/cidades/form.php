<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-map-marker-alt mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($cidade) ? base_url('admin/cidades/atualizar/' . $cidade['cid_id']) : base_url('admin/cidades/salvar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="cid_nome">Nome da Cidade <span class="text-danger">*</span></label>
                        <input type="text" name="cid_nome" id="cid_nome" class="form-control" value="<?= esc($cidade['cid_nome'] ?? old('cid_nome') ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cid_estado">Estado (UF) <span class="text-danger">*</span></label>
                        <select name="cid_estado" id="cid_estado" class="form-control" required>
                            <?php foreach ($estados as $e): ?>
                                <option value="<?= $e['est_id'] ?>" <?= (!empty($cidade) && (int)$cidade['cid_estado'] === (int)$e['est_id']) ? 'selected' : '' ?>>
                                    <?= esc($e['est_nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cid_url">Slug URL (Deixe em branco para gerar automaticamente)</label>
                        <input type="text" name="cid_url" id="cid_url" class="form-control" value="<?= esc($cidade['cid_url'] ?? old('cid_url') ?? '') ?>" placeholder="ex: piracicaba, campinas, americana">
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/cidades') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
