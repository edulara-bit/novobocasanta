<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\EmailService;
use CodeIgniter\Controller;

class Debug extends Controller
{
    protected const DEBUG_KEY = 'bocasanta2026';

    /**
     * Valida chave de segurança
     */
    protected function isAuthorized(): bool
    {
        $session = session();
        if ($session->get('admin_logged') || $session->get('debug_authorized')) {
            return true;
        }

        $key = $this->request->getGetPost('key');
        if ($key === self::DEBUG_KEY) {
            $session->set('debug_authorized', true);
            return true;
        }

        return false;
    }

    /**
     * Renderiza tela de login da chave caso não autorizado
     */
    protected function renderAuthPrompt(string $title): string
    {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title} - Boca Santa Ofertas</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light d-flex align-items-center justify-content-center' style='min-height: 100vh;'>
            <div class='card shadow-sm' style='max-width: 420px; width: 100%;'>
                <div class='card-header bg-dark text-white text-center py-3'>
                    <h5 class='mb-0'>{$title}</h5>
                </div>
                <div class='card-body p-4'>
                    <p class='text-muted small text-center mb-3'>Área de diagnóstico restrita. Digite a chave de segurança para continuar.</p>
                    <form method='GET'>
                        <div class='mb-3'>
                            <label class='form-label small fw-bold'>Chave de Acesso:</label>
                            <input type='password' name='key' class='form-control' placeholder='Chave de segurança' required autofocus>
                        </div>
                        <button type='submit' class='btn btn-danger w-100 fw-bold'>Acessar Diagnóstico</button>
                    </form>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Diagnóstico completo de SMTP e envio de teste em tempo real
     */
    public function email()
    {
        if (!$this->isAuthorized()) {
            return $this->renderAuthPrompt('Diagnóstico de E-mail & SMTP');
        }

        $emailSvc = new EmailService();
        $diagnoseResults = $emailSvc->diagnose();

        $sendResult = null;
        $transcript = [];
        $lastError = '';

        $isPost = strtoupper($this->request->getMethod()) === 'POST' || $this->request->getGet('send') === '1';
        $recipients = $this->request->getPost('to') ?: $this->request->getGet('to') ?: 'vendas@bocasantaofertas.com.br, loweltrevisan@gmail.com';

        if ($isPost) {
            $toAddresses = array_map('trim', explode(',', (string)$recipients));
            $toAddresses = array_filter($toAddresses);

            $subject = 'Teste de Diagnóstico SMTP - ' . date('d/m/Y H:i:s');
            $body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                    <h2 style='color: #dc2626; margin-top: 0;'>Teste de Diagnóstico SMTP</h2>
                    <p>Este é um e-mail de teste disparado pelo painel de diagnóstico em produção (Boca Santa Ofertas).</p>
                    <p><strong>Data/Hora do Disparo:</strong> " . date('d/m/Y H:i:s') . "</p>
                    <p><strong>Host do Servidor:</strong> " . gethostname() . " (IP: " . ($this->request->getIPAddress()) . ")</p>
                    <p style='color: #16a34a; font-weight: bold;'>Se você recebeu esta mensagem, a comunicação SMTP está 100% operacional!</p>
                </div>";

            $sendResult = $emailSvc->send(
                to: $toAddresses,
                subject: $subject,
                htmlBody: $body,
                fromEmail: 'contato@bocasantaofertas.com.br',
                fromName: 'Diagnóstico Boca Santa',
                replyToEmail: 'contato@bocasantaofertas.com.br',
                replyToName: 'Diagnóstico Boca Santa'
            );

            $transcript = $emailSvc->getTranscript();
            $lastError = $emailSvc->getLastError();
        }

        // Variáveis de ambiente e sistema
        $envInfo = [
            'PHP_VERSION'      => PHP_VERSION,
            'OPENSSL_VERSION'  => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'N/A',
            'OPENSSL_ENABLED'  => extension_loaded('openssl') ? 'Sim' : 'Não',
            'SOCKETS_ENABLED'  => function_exists('stream_socket_client') ? 'Sim' : 'Não',
            'SERVER_HOST'      => gethostname(),
            'SERVER_ADDR'      => $_SERVER['SERVER_ADDR'] ?? 'N/A',
            'SMTP_HOST_ENV'    => getenv('SMTP_HOST') ?: '(não definido - usando poste / fallback)',
            'SMTP_PORT_ENV'    => getenv('SMTP_PORT') ?: '587',
            'SMTP_USER_ENV'    => getenv('SMTP_USER') ?: 'contato@bocasantaofertas.com.br',
            'LOGS_WRITABLE'    => is_writable(WRITEPATH . 'logs') ? 'Sim (Permissão OK)' : 'NÃO (Sem permissão de escrita!)',
        ];

        return view('debug/email', [
            'envInfo'         => $envInfo,
            'diagnoseResults' => $diagnoseResults,
            'isPost'          => $isPost,
            'sendResult'      => $sendResult,
            'transcript'      => $transcript,
            'lastError'       => $lastError,
            'recipients'      => $recipients,
            'debugKey'        => self::DEBUG_KEY,
        ]);
    }

    /**
     * Visualizador em tempo real dos arquivos de log do CodeIgniter 4
     */
    public function logs()
    {
        if (!$this->isAuthorized()) {
            return $this->renderAuthPrompt('Logs do CodeIgniter 4');
        }

        $logPath = WRITEPATH . 'logs/';
        $files = array_merge(
            glob($logPath . 'log-*.php') ?: [],
            glob($logPath . 'log-*.log') ?: []
        );
        $files = array_unique($files);
        rsort($files);

        $selectedFile = (string)$this->request->getGet('file');
        $fileNames = array_map('basename', $files);

        if (empty($selectedFile) || !in_array($selectedFile, $fileNames, true)) {
            $selectedFile = !empty($fileNames) ? $fileNames[0] : '';
        }

        $filter = (string)$this->request->getGet('filter');
        $level = (string)$this->request->getGet('level');

        $lines = [];
        $fileSize = 0;
        $lastModified = '';

        if (!empty($selectedFile) && file_exists($logPath . $selectedFile)) {
            $filePath = $logPath . $selectedFile;
            try {
                $fileSize = filesize($filePath) ?: 0;
                $lastModified = date('d/m/Y H:i:s', filemtime($filePath) ?: time());

                $rawContent = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

                // Filtro por texto e nível
                foreach ($rawContent as $rawLine) {
                    // Ignora cabeçalhos PHP de segurança do CodeIgniter
                    if (str_contains($rawLine, 'defined(\'SYSTEMPATH\')') || str_contains($rawLine, '<?php')) {
                        continue;
                    }
                    if (!empty($level) && !str_contains($rawLine, $level)) {
                        continue;
                    }
                    if (!empty($filter) && stripos($rawLine, $filter) === false) {
                        continue;
                    }
                    $lines[] = $rawLine;
                }

                // Pega as últimas 500 linhas e inverte para o topo ser o mais recente
                $lines = array_slice($lines, -500);
                $lines = array_reverse($lines);
            } catch (\Throwable $e) {
                $lines[] = 'Erro ao ler arquivo de log: ' . $e->getMessage();
            }
        }

        return view('debug/logs', [
            'files'        => $fileNames,
            'selectedFile' => $selectedFile,
            'lines'        => $lines,
            'fileSize'     => $fileSize,
            'lastModified' => $lastModified,
            'filter'       => $filter,
            'level'        => $level,
            'isWritable'   => is_writable($logPath),
            'debugKey'     => self::DEBUG_KEY,
        ]);
    }
}
