<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico SMTP & E-mail - Boca Santa Ofertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .card { border-radius: 10px; border: 1px solid #e2e8f0; }
        .card-header { border-top-left-radius: 10px; border-top-right-radius: 10px; }
        .terminal { background-color: #0f172a; color: #38bdf8; font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; line-height: 1.6; border-radius: 8px; padding: 16px; max-height: 480px; overflow-y: auto; }
        .badge-status { font-size: 12px; padding: 5px 10px; }
    </style>
</head>
<body class="py-4">
<div class="container-fluid px-4" style="max-width: 1280px;">

    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="fas fa-stethoscope text-danger me-2"></i> Diagnóstico Operacional SMTP & E-mail
            </h3>
            <p class="text-muted small mb-0">Ferramenta em tempo real para testes de conexão de rede, portas e envio de mensagens na VPS.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('debug/logs?key=' . $debugKey) ?>" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-terminal me-1"></i> Ver Logs CI4
            </a>
            <a href="<?= base_url('anuncie') ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i> Página /anuncie
            </a>
        </div>
    </div>

    <!-- Alert de Resultado de Envio (se houver) -->
    <?php if ($isPost): ?>
        <?php if ($sendResult === true): ?>
            <div class="alert alert-success shadow-sm d-flex align-items-center mb-4">
                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">E-mail disparado e entregue com sucesso!</h5>
                    <p class="mb-0 small">O servidor SMTP autenticou e aceitou a mensagem para todos os destinatários informados.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger shadow-sm d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Falha no disparo do e-mail!</h5>
                    <p class="mb-0 small"><strong>Erro registrado:</strong> <?= esc($lastError ?: 'Erro desconhecido durante a comunicação SMTP.') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Transcrição SMTP -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-stream me-2"></i> Transcrição da Conversa SMTP em Tempo Real</span>
                <span class="badge bg-secondary"><?= count($transcript) ?> eventos</span>
            </div>
            <div class="card-body p-0">
                <div class="terminal">
                    <?php foreach ($transcript as $line): ?>
                        <?php
                            $color = '#38bdf8';
                            if (str_contains($line, 'ERRO') || str_contains($line, 'FALHA')) {
                                $color = '#f87171';
                            } elseif (str_contains($line, 'SUCESSO') || str_contains($line, 'Autenticado') || str_contains($line, '250')) {
                                $color = '#4ade80';
                            } elseif (str_contains($line, '---')) {
                                $color = '#fbbf24';
                            }
                        ?>
                        <div style="color: <?= $color ?>;"><?= esc($line) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Coluna Esquerda: Conectividade com Alvos -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fas fa-network-wired text-primary me-2"></i> Matriz de Conectividade de Portas & Hosts
                    </h5>
                    <small class="text-muted">Testando sockets a partir de dentro do container <code>bocasanta-app</code>.</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Alvo (Host:Porta)</th>
                                <th>IP Resolvido</th>
                                <th>Status</th>
                                <th>Latência</th>
                                <th>Resposta / Banner</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php foreach ($diagnoseResults as $res): ?>
                                <tr>
                                    <td class="fw-bold font-monospace"><?= esc($res['target']) ?></td>
                                    <td><code><?= esc($res['dns_ip']) ?></code></td>
                                    <td>
                                        <?php if ($res['status'] === 'OK'): ?>
                                            <span class="badge bg-success badge-status"><i class="fas fa-check me-1"></i> OK</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger badge-status"><i class="fas fa-times me-1"></i> Falha</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= esc($res['latency']) ?></td>
                                    <td class="text-truncate" style="max-width: 260px;" title="<?= esc($res['banner']) ?>">
                                        <code class="<?= $res['status'] === 'OK' ? 'text-success' : 'text-danger' ?>">
                                            <?= esc($res['banner']) ?>
                                        </code>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light small text-muted">
                    <i class="fas fa-info-circle me-1"></i> Alvos verdes com portas 587 ou 25 estão prontos para autenticação e envio de e-mails.
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Formulário de Envio de Teste & Ambiente -->
        <div class="col-lg-5">
            <!-- Formulário de Teste -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fas fa-paper-plane text-danger me-2"></i> Disparar E-mail de Teste Agora
                    </h5>
                    <small class="text-muted">Simula o envio comercial com relatório imediato.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('debug/email?key=' . $debugKey) ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Destinatários (separados por vírgula):</label>
                            <input type="text" name="to" class="form-control" value="<?= esc($recipients) ?>" required>
                            <div class="form-text small">Ex: vendas@bocasantaofertas.com.br, loweltrevisan@gmail.com</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                            <i class="fas fa-paper-plane me-2"></i> Enviar E-mail de Teste
                        </button>
                    </form>
                </div>
            </div>

            <!-- Dados do Ambiente -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="fas fa-server text-secondary me-2"></i> Ambiente PHP & Servidor
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        <?php foreach ($envInfo as $k => $v): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold text-muted"><?= esc($k) ?></span>
                                <span class="font-monospace text-dark"><?= esc($v) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
