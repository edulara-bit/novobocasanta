<?php

declare(strict_types=1);

namespace App\Services;

class EmailService
{
    protected string $host;
    protected int $port;
    protected string $user;
    protected string $pass;
    protected int $timeout;

    /** @var list<string> */
    protected array $transcript = [];
    protected string $lastError = '';

    public function __construct(
        ?string $host = null,
        ?int $port = null,
        ?string $user = null,
        ?string $pass = null,
        int $timeout = 4
    ) {
        $this->host = $host ?: (getenv('SMTP_HOST') ?: 'poste');
        $this->port = $port ?: (int)(getenv('SMTP_PORT') ?: 587);
        $this->user = $user ?: (getenv('SMTP_USER') ?: 'contato@bocasantaofertas.com.br');
        $this->pass = $pass ?: (getenv('SMTP_PASS') ?: 'Lppdc5FoeG');
        $this->timeout = $timeout;
    }

    /**
     * Registra logs com segurança mesmo se log_message do CI4 não estiver disponível
     */
    protected function log(string $level, string $message): void
    {
        if (function_exists('log_message')) {
            log_message($level, $message);
        } else {
            error_log("[{$level}] {$message}");
        }
    }

    public function getTranscript(): array
    {
        return $this->transcript;
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Retorna a lista ordenada de alvos (host + porta + tls) para tentativa de conexão
     */
    public function getCandidateTargets(): array
    {
        $defaultTargets = [
            ['host' => $this->host, 'port' => $this->port, 'tls' => true],
            ['host' => 'poste', 'port' => 587, 'tls' => true],
            ['host' => 'poste', 'port' => 25, 'tls' => true],
            ['host' => '129.121.41.139', 'port' => 587, 'tls' => true],
            ['host' => '129.121.41.139', 'port' => 25, 'tls' => true],
            ['host' => '172.17.0.1', 'port' => 587, 'tls' => true],
            ['host' => '172.17.0.1', 'port' => 25, 'tls' => true],
            ['host' => 'mail.cidadepremium.com.br', 'port' => 587, 'tls' => true],
            ['host' => 'mail.linefast.com.br', 'port' => 587, 'tls' => true],
        ];

        // Deduplica alvos baseado em host + porta + tls
        $seen = [];
        $unique = [];
        foreach ($defaultTargets as $target) {
            $key = "{$target['host']}:{$target['port']}:" . ($target['tls'] ? '1' : '0');
            if (!isset($seen[$key]) && !empty($target['host'])) {
                $seen[$key] = true;
                $unique[] = $target;
            }
        }

        return $unique;
    }

    /**
     * Envia um e-mail formatado via SMTP autenticado com fallback multi-alvo
     */
    public function send(
        string|array $to,
        string $subject,
        string $htmlBody,
        string $fromEmail = 'contato@bocasantaofertas.com.br',
        string $fromName = 'Boca Santa Ofertas',
        ?string $replyToEmail = null,
        ?string $replyToName = null
    ): bool {
        $this->transcript = [];
        $this->lastError = '';

        $toAddresses = is_array($to) ? $to : [$to];
        $replyToEmail = $replyToEmail ?: $fromEmail;
        $replyToName = $replyToName ?: $fromName;

        $targets = $this->getCandidateTargets();
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);

        $attemptErrors = [];

        foreach ($targets as $target) {
            $host = $target['host'];
            $port = (int)$target['port'];
            $useTls = (bool)$target['tls'];
            $targetDesc = "{$host}:{$port} (TLS: " . ($useTls ? 'SIM' : 'NÃO') . ")";

            $this->transcript[] = "--- Tentando conexão com {$targetDesc} ---";

            $fp = @stream_socket_client(
                "tcp://{$host}:{$port}",
                $errno,
                $errstr,
                (float)$this->timeout,
                STREAM_CLIENT_CONNECT,
                $ctx
            );

            if (!$fp) {
                $err = "Falha de socket: {$errstr} ({$errno})";
                $this->transcript[] = "  ERRO: {$err}";
                $attemptErrors[] = "{$targetDesc} -> {$err}";
                continue;
            }

            stream_set_timeout($fp, 10);

            try {
                // 1. Banner
                $banner = $this->expectResponse($fp, ['220']);
                $this->transcript[] = "  << {$banner}";

                // 2. EHLO
                $ehlo = $this->sendCommand($fp, "EHLO bocasantaofertas.com.br", ['250']);
                $this->transcript[] = "  >> EHLO bocasantaofertas.com.br";
                $this->transcript[] = "  << {$ehlo}";

                // 3. STARTTLS se habilitado
                if ($useTls) {
                    $this->transcript[] = "  >> STARTTLS";
                    $starttlsResp = $this->sendCommand($fp, "STARTTLS", ['220']);
                    $this->transcript[] = "  << {$starttlsResp}";

                    $crypto = @stream_socket_enable_crypto(
                        $fp,
                        true,
                        STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT
                    );

                    if (!$crypto) {
                        throw new \RuntimeException("Falha na negociação TLS.");
                    }
                    $this->transcript[] = "  [TLS ativado com sucesso]";

                    // Re-envia EHLO pós TLS
                    $ehlo2 = $this->sendCommand($fp, "EHLO bocasantaofertas.com.br", ['250']);
                    $this->transcript[] = "  >> EHLO bocasantaofertas.com.br";
                    $this->transcript[] = "  << {$ehlo2}";
                }

                // 4. AUTH LOGIN
                $this->transcript[] = "  >> AUTH LOGIN";
                $this->sendCommand($fp, "AUTH LOGIN", ['334']);
                $this->sendCommand($fp, base64_encode($this->user), ['334']);
                $authResp = $this->sendCommand($fp, base64_encode($this->pass), ['235']);
                $this->transcript[] = "  << {$authResp} (Autenticado como {$this->user})";

                // 5. MAIL FROM
                $actualFrom = $this->user;
                $mailFromResp = $this->sendCommand($fp, "MAIL FROM:<{$actualFrom}>", ['250']);
                $this->transcript[] = "  >> MAIL FROM:<{$actualFrom}> -> {$mailFromResp}";

                // 6. RCPT TO
                foreach ($toAddresses as $recipient) {
                    $cleanRecipient = trim(preg_replace('/[^\w\.\-\+@]/', '', $recipient));
                    if (!empty($cleanRecipient)) {
                        $rcptResp = $this->sendCommand($fp, "RCPT TO:<{$cleanRecipient}>", ['250', '251']);
                        $this->transcript[] = "  >> RCPT TO:<{$cleanRecipient}> -> {$rcptResp}";
                    }
                }

                // 7. DATA
                $dataPrompt = $this->sendCommand($fp, "DATA", ['354']);
                $this->transcript[] = "  >> DATA -> {$dataPrompt}";

                // 8. Envio MIME com normalização rigorosa para CRLF (RFC 5321 / RFC 2822)
                // O Haraka/Poste.io rejeita terminantemente qualquer "\n" avulso com "451 Bare line-feed"
                $normalizedBody = str_replace(["\r\n", "\r"], "\n", $htmlBody);
                $normalizedBody = preg_replace('/^\./m', '..', $normalizedBody);
                $normalizedBody = str_replace("\n", "\r\n", $normalizedBody);

                $headers = [];
                $headers[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$actualFrom}>";
                $headers[] = "To: " . implode(', ', $toAddresses);
                $headers[] = "Reply-To: =?UTF-8?B?" . base64_encode($replyToName) . "?= <{$replyToEmail}>";
                $headers[] = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=";
                $headers[] = "Date: " . date('r');
                $headers[] = "MIME-Version: 1.0";
                $headers[] = "Content-Type: text/html; charset=UTF-8";
                $headers[] = "Content-Transfer-Encoding: 8bit";
                $headers[] = "X-Mailer: BocaSanta/1.0 (PHP/" . PHP_VERSION . ")";

                $messageData = implode("\r\n", $headers) . "\r\n\r\n" . $normalizedBody . "\r\n.\r\n";
                $this->sendRaw($fp, $messageData);
                $finalResp = $this->expectResponse($fp, ['250']);
                $this->transcript[] = "  << {$finalResp}";

                // 9. QUIT
                $this->sendRaw($fp, "QUIT\r\n");
                @fclose($fp);

                $this->transcript[] = "SUCESSO: E-mail entregue com sucesso via {$targetDesc}!";
                $this->log('info', "[EmailService] E-mail enviado com sucesso via SMTP ({$targetDesc}) para: " . implode(', ', $toAddresses));
                return true;

            } catch (\Throwable $e) {
                $err = "Erro SMTP em {$targetDesc}: " . $e->getMessage();
                $this->transcript[] = "  FALHA: {$err}";
                $attemptErrors[] = $err;
                @fclose($fp);
                // Continua para o próximo alvo do loop
            }
        }

        $this->lastError = implode(' | ', $attemptErrors);
        $this->log('error', "[EmailService] Todos os alvos SMTP falharam. Detalhes: {$this->lastError}");
        return false;
    }

    /**
     * Realiza um diagnóstico detalhado de conectividade e portas
     */
    public function diagnose(): array
    {
        $results = [];
        $targets = $this->getCandidateTargets();

        foreach ($targets as $target) {
            $host = $target['host'];
            $port = (int)$target['port'];
            $desc = "{$host}:{$port}";

            $ip = @gethostbyname($host);
            $dnsResolved = ($ip !== $host);

            $t0 = microtime(true);
            $errno = 0;
            $errstr = '';
            $fp = @stream_socket_client(
                "tcp://{$host}:{$port}",
                $errno,
                $errstr,
                2.5,
                STREAM_CLIENT_CONNECT
            );
            $timeMs = round((microtime(true) - $t0) * 1000, 1);

            if ($fp) {
                stream_set_timeout($fp, 3);
                $banner = trim((string)@fgets($fp, 512));
                @fclose($fp);

                $results[] = [
                    'target'   => $desc,
                    'dns_ip'   => $ip,
                    'status'   => 'OK',
                    'latency'  => "{$timeMs}ms",
                    'banner'   => $banner ?: '(sem banner)',
                    'can_send' => true,
                ];
            } else {
                $results[] = [
                    'target'   => $desc,
                    'dns_ip'   => $dnsResolved ? $ip : 'Não resolveu DNS',
                    'status'   => 'FALHA',
                    'latency'  => "{$timeMs}ms",
                    'banner'   => "Erro: {$errstr} ({$errno})",
                    'can_send' => false,
                ];
            }
        }

        return $results;
    }

    protected function sendCommand($fp, string $cmd, array $expectedCodes): string
    {
        $this->sendRaw($fp, $cmd . "\r\n");
        return $this->expectResponse($fp, $expectedCodes);
    }

    protected function sendRaw($fp, string $data): void
    {
        fwrite($fp, $data);
        fflush($fp);
    }

    protected function expectResponse($fp, array $expectedCodes): string
    {
        $response = '';
        while ($line = fgets($fp, 512)) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }

        $code = substr(trim($response), 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new \RuntimeException("Resposta SMTP inesperada (esperado " . implode(',', $expectedCodes) . "): " . trim($response));
        }

        return trim($response);
    }
}
