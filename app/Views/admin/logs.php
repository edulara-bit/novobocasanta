<?= $this->extend('admin/layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-file-code text-danger mr-2"></i> Registro de Logs do CodeIgniter 4
                </h3>
                <div class="card-tools d-flex align-items-center gap-2">
                    <form method="GET" action="<?= base_url('admin/logs') ?>" class="form-inline">
                        <label class="mr-2 font-weight-normal text-muted">Arquivo:</label>
                        <select name="file" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                            <?php foreach ($files as $f): ?>
                                <option value="<?= esc($f) ?>" <?= $f === $selectedFile ? 'selected' : '' ?>><?= esc($f) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($lines)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-info-circle fa-2x mb-2 text-secondary"></i>
                        <p class="mb-0">Nenhum registro encontrado no arquivo <strong><?= esc($selectedFile ?: 'selecionado') ?></strong>.</p>
                    </div>
                <?php else: ?>
                    <div style="background-color: #1e1e1e; color: #d4d4d4; font-family: 'Consolas', 'Courier New', monospace; font-size: 12.5px; padding: 15px; max-height: 600px; overflow-y: auto; line-height: 1.5;">
                        <?php foreach ($lines as $line): ?>
                            <?php
                                $class = 'text-light';
                                if (str_contains($line, 'CRITICAL') || str_contains($line, 'ERROR')) {
                                    $class = 'text-danger font-weight-bold';
                                } elseif (str_contains($line, 'WARNING')) {
                                    $class = 'text-warning';
                                } elseif (str_contains($line, 'Crawler') || str_contains($line, 'facebook')) {
                                    $class = 'text-info font-weight-bold';
                                } elseif (str_contains($line, 'INFO')) {
                                    $class = 'text-secondary';
                                }
                            ?>
                            <div class="<?= $class ?>" style="word-break: break-all; margin-bottom: 3px;"><?= esc($line) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-muted small">
                Exibindo as últimas <?= count($lines) ?> linhas do arquivo <code>writable/logs/<?= esc($selectedFile) ?></code> (da mais recente para a mais antiga).
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
