<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-directions mr-1"></i> <?= esc($title) ?>
                </h3>
            </div>
            
            <form action="<?= !empty($red) ? base_url('admin/redirecionamentos/atualizar/' . $red['red_id']) : base_url('admin/redirecionamentos/salvar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group">
                        <label for="red_titulo">Título / Motivo do Redirecionamento</label>
                        <input type="text" name="red_titulo" id="red_titulo" class="form-control" value="<?= esc($red['red_titulo'] ?? old('red_titulo') ?? '') ?>" placeholder="ex: Migração de produto antigo">
                    </div>

                    <div class="form-group">
                        <label for="red_link_antigo">URL Antiga (Origem) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?= base_url() ?></span>
                            </div>
                            <input type="text" name="red_link_antigo" id="red_link_antigo" class="form-control" value="<?= esc($red['red_link_antigo'] ?? old('red_link_antigo') ?? '') ?>" placeholder="oferta/mostra/123 ou produto-antigo" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="red_link_novo">URL Nova (Destino) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?= base_url() ?></span>
                            </div>
                            <input type="text" name="red_link_novo" id="red_link_novo" class="form-control" value="<?= esc($red['red_link_novo'] ?? old('red_link_novo') ?? '') ?>" placeholder="piracicaba/categoria/alimentacao" required>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= base_url('admin/redirecionamentos') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Voltar</a>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4"><i class="fas fa-save mr-1"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
