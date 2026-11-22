<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs do CodeIgniter 4 - Boca Santa Ofertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .card { border-radius: 10px; border: 1px solid #334155; background-color: #1e293b; }
        .card-header { border-top-left-radius: 10px; border-top-right-radius: 10px; background-color: #1e293b; border-bottom: 1px solid #334155; }
        .terminal { background-color: #090d16; color: #cbd5e1; font-family: 'Consolas', 'Courier New', monospace; font-size: 12.5px; line-height: 1.6; border-radius: 8px; padding: 18px; max-height: 680px; overflow-y: auto; }
        .log-error { color: #f87171; font-weight: bold; }
        .log-warning { color: #fbbf24; }
        .log-info { color: #60a5fa; }
        .log-debug { color: #94a3b8; }
        .log-critical { color: #ef4444; background: rgba(239, 68, 68, 0.15); padding: 2px 4px; border-radius: 3px; font-weight: bold; }
        .form-control, .form-select { background-color: #0f172a; border-color: #334155; color: #f8fafc; }
        .form-control:focus, .form-select:focus { background-color: #0f172a; border-color: #dc2626; color: #f8fafc; box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.25); }
    </style>
</head>
<body class="py-4">
<div class="container-fluid px-4" style="max-width: 1400px;">

    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
        <div>
            <h3 class="fw-bold mb-1 text-white">
                <i class="fas fa-terminal text-danger me-2"></i> Visualizador de Logs do CodeIgniter 4
            </h3>
            <p class="text-secondary small mb-0">Inspeção ao vivo dos arquivos em <code>writable/logs/</code> em produção.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('debug/email?key=' . $debugKey) ?>" class="btn btn-outline-info btn-sm">
                <i class="fas fa-stethoscope me-1"></i> Diagnóstico SMTP
            </a>
            <a href="<?= base_url('anuncie') ?>" target="_blank" class="btn btn-outline-light btn-sm">
                <i class="fas fa-external-link-alt me-1"></i> Abrir /anuncie
            </a>
        </div>
    </div>

    <!-- Status de Permissão de Escrita -->
    <?php if (!$isWritable): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <strong>Atenção:</strong> O diretório <code>writable/logs/</code> NÃO possui permissão de escrita para o usuário do servidor web (Apache/PHP). Os logs não estão sendo gravados no disco!
            </div>
        </div>
    <?php endif; ?>

    <!-- Barra de Filtros e Seleção -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body py-3">
            <form method="GET" action="<?= base_url('debug/logs') ?>" class="row g-2 align-items-center">
                <input type="hidden" name="key" value="<?= esc($debugKey) ?>">
                
                <div class="col-md-3">
                    <label class="small text-secondary fw-bold mb-1">Arquivo de Log:</label>
                    <select name="file" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php foreach ($files as $f): ?>
                            <option value="<?= esc($f) ?>" <?= $f === $selectedFile ? 'selected' : '' ?>><?= esc($f) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small text-secondary fw-bold mb-1">Nível:</label>
                    <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="" <?= empty($level) ? 'selected' : '' ?>>Todos os Níveis</option>
                        <option value="CRITICAL" <?= $level === 'CRITICAL' ? 'selected' : '' ?>>CRITICAL</option>
                        <option value="ERROR" <?= $level === 'ERROR' ? 'selected' : '' ?>>ERROR</option>
                        <option value="WARNING" <?= $level === 'WARNING' ? 'selected' : '' ?>>WARNING</option>
                        <option value="INFO" <?= $level === 'INFO' ? 'selected' : '' ?>>INFO</option>
                        <option value="DEBUG" <?= $level === 'DEBUG' ? 'selected' : '' ?>>DEBUG</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="small text-secondary fw-bold mb-1">Pesquisar Texto / Palavra-chave:</label>
                    <input type="text" name="filter" class="form-control form-control-sm" placeholder="Ex: Anuncie, EmailService, SMTP, etc." value="<?= esc($filter) ?>">
                </div>

                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-danger btn-sm flex-grow-1">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="<?= base_url('debug/logs?key=' . $debugKey . '&file=' . urlencode($selectedFile)) ?>" class="btn btn-secondary btn-sm" title="Limpar Filtro">
                        <i class="fas fa-undo"></i>
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="location.reload()" title="Recarregar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Terminal de Logs -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <div>
                <span class="fw-bold text-white"><i class="fas fa-file-alt text-danger me-2"></i> <?= esc($selectedFile ?: 'Nenhum arquivo encontrado') ?></span>
                <?php if ($fileSize > 0): ?>
                    <span class="badge bg-secondary ms-2"><?= round($fileSize / 1024, 1) ?> KB</span>
                    <span class="text-secondary small ms-2">Última modificação: <?= esc($lastModified) ?></span>
                <?php endif; ?>
            </div>
            <div>
                <span class="badge bg-dark border border-secondary text-secondary">
                    <?= count($lines) ?> linha(s) exibida(s) (mais recente no topo)
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($lines)): ?>
                <div class="p-5 text-center text-secondary">
                    <i class="fas fa-info-circle fa-2x mb-3 text-secondary"></i>
                    <p class="mb-0">Nenhum registro encontrado no arquivo <strong><?= esc($selectedFile ?: 'selecionado') ?></strong> com os filtros aplicados.</p>
                </div>
            <?php else: ?>
                <div class="terminal">
                    <?php foreach ($lines as $line): ?>
                        <?php
                            $class = 'log-debug';
                            if (str_contains($line, 'CRITICAL')) {
                                $class = 'log-critical';
                            } elseif (str_contains($line, 'ERROR')) {
                                $class = 'log-error';
                            } elseif (str_contains($line, 'WARNING')) {
                                $class = 'log-warning';
                            } elseif (str_contains($line, 'INFO')) {
                                $class = 'log-info';
                            }
                        ?>
                        <div class="<?= $class ?>" style="word-break: break-all; margin-bottom: 2px;">
                            <?= esc($line) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>
