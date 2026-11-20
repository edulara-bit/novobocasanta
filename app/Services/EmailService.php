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

    public function __construct(
        ?string $host = null,
        ?int $port = null,
        ?string $user = null,
        ?string $pass = null,
        int $timeout = 10
    ) {
        $this->host = $host ?: (getenv('SMTP_HOST') ?: '129.121.41.139');
        $this->port = $port ?: (int)(getenv('SMTP_PORT') ?: 587);
        $this->user = $user ?: (getenv('SMTP_USER') ?: 'contato@bocasantaofertas.com.br');
        $this->pass = $pass ?: (getenv('SMTP_PASS') ?: 'Lppdc5FoeG');
        $this->timeout = $timeout;
    }

    /**
     * Envia um e-mail formatado via SMTP autenticado (Poste.io / Haraka porta 587)
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
        $toAddresses = is_array($to) ? $to : [$to];
        $replyToEmail = $replyToEmail ?: $fromEmail;
        $replyToName = $replyToName ?: $fromName;

        // Lista de hosts para tentar (IP público e nome interno do serviço no Docker)
        $hosts = array_unique(array_filter([$this->host, 'poste', '129.121.41.139', 'mail.linefast.com.br']));

        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);

        $fp = false;
        $lastErr = '';

        foreach ($hosts as $tryHost) {
            $fp = @stream_socket_client(
                "tcp://{$tryHost}:{$this->port}",
                $errno,
                $errstr,
                $this->timeout,
                STREAM_CLIENT_CONNECT,
                $ctx
            );
            if ($fp) {
                break;
            }
            $lastErr = "$errstr ($errno)";
        }

        if (!$fp) {
            log_message('error', "[EmailService] Falha ao conectar ao SMTP em todos os hosts testados: $lastErr");
            return false;
        }

        stream_set_timeout($fp, $this->timeout);

        try {
            $this->expectResponse($fp, ['220']);

            // EHLO
            $this->sendCommand($fp, "EHLO bocasantaofertas.com.br", ['250']);

            // STARTTLS
            $this->sendCommand($fp, "STARTTLS", ['220']);
            $crypto = @stream_socket_enable_crypto(
                $fp,
                true,
                STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT
            );

            if (!$crypto) {
                log_message('error', "[EmailService] Falha na negociação TLS com o servidor SMTP.");
                fclose($fp);
                return false;
            }

            // EHLO após TLS
            $this->sendCommand($fp, "EHLO bocasantaofertas.com.br", ['250']);

            // AUTH LOGIN
            $this->sendCommand($fp, "AUTH LOGIN", ['334']);
            $this->sendCommand($fp, base64_encode($this->user), ['334']);
            $this->sendCommand($fp, base64_encode($this->pass), ['235']);

            // MAIL FROM (Envelope sender)
            $this->sendCommand($fp, "MAIL FROM:<{$this->user}>", ['250']);

            // RCPT TO (para cada destinatário)
            foreach ($toAddresses as $recipient) {
                $cleanRecipient = trim(preg_replace('/[^\w\.\-\+@]/', '', $recipient));
                if (!empty($cleanRecipient)) {
                    $this->sendCommand($fp, "RCPT TO:<{$cleanRecipient}>", ['250', '251']);
                }
            }

            // DATA
            $this->sendCommand($fp, "DATA", ['354']);

            // Montar Headers e Mensagem MIME
            // Nota: Para cumprir a validação do Haraka/Poste.io, o header From deve ser o usuário autenticado ($this->user)
            $actualFrom = $this->user;
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

            $messageData = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.\r\n";

            $this->sendRaw($fp, $messageData);
            $this->expectResponse($fp, ['250']);

            // QUIT
            $this->sendRaw($fp, "QUIT\r\n");
            @fclose($fp);

            log_message('info', "[EmailService] E-mail enviado com sucesso via SMTP para: " . implode(', ', $toAddresses));
            return true;
        } catch (\Throwable $e) {
            log_message('error', "[EmailService] Erro durante transação SMTP: " . $e->getMessage());
            @fclose($fp);
            return false;
        }
    }

    protected function sendCommand($fp, string $cmd, array $expectedCodes): string
    {
        $this->sendRaw($fp, $cmd . "\r\n");
        return $this->expectResponse($fp, $expectedCodes);
    }

    protected function sendRaw($fp, string $data): void
    {
        fputs($fp, $data);
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

        $code = substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new \RuntimeException("Resposta SMTP inesperada (esperado " . implode(',', $expectedCodes) . "): " . trim($response));
        }

        return $response;
    }
}
