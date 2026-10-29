<?php

declare(strict_types=1);

namespace App\Services;

class LinecashWhatsappService
{
    /**
     * Limpa o número de telefone para o padrão DDI + DDD + Número (ex: 5519999998888)
     */
    public static function formatarNumeroWhatsapp(string $telefone): string
    {
        $digitos = preg_replace('/\D/', '', $telefone);
        if (empty($digitos)) {
            return '';
        }

        // Se tem 10 ou 11 dígitos, adiciona o DDI 55 do Brasil
        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            return '55' . $digitos;
        }

        return $digitos;
    }

    /**
     * Monta a mensagem de WhatsApp para notificação de cashback recebido no PDV
     */
    public static function gerarMensagemCashback(
        string $clienteNome,
        string $lojaNome,
        float $valorCompra,
        float $valorLc,
        float $saldoTotalLc,
        ?string $produtoDestaqueTitulo = null,
        ?string $produtoDestaqueUrl = null,
        ?string $msgAgradecimento = null
    ): string {
        $saudacao = !empty($clienteNome) ? "Olá, *{$clienteNome}*!" : "Olá!";
        $lcFormatado = number_format($valorLc, 2, ',', '.');
        $compraFormatada = number_format($valorCompra, 2, ',', '.');
        $saldoFormatado = number_format($saldoTotalLc, 2, ',', '.');

        $msg = "🎉 {$saudacao}\n\n";
        $msg .= "Você acaba de ganhar *{$lcFormatado} LC* em cashback pela sua compra de R$ {$compraFormatada} na loja *{$lojaNome}*!\n\n";
        $msg .= "💰 *Seu Saldo Disponível:* {$saldoFormatado} LC (R$ {$saldoFormatado})\n";
        $msg .= "_(1 LC = R$ 1,00 para usar em compras nas lojas parceiras)_\n\n";

        if (!empty($produtoDestaqueTitulo)) {
            $msg .= "✨ *Destaque de hoje no LineFast:*\n";
            $msg .= "👉 {$produtoDestaqueTitulo}\n";
            if (!empty($produtoDestaqueUrl)) {
                $msg .= "🔗 {$produtoDestaqueUrl}\n\n";
            } else {
                $msg .= "\n";
            }
        }

        if (!empty($msgAgradecimento)) {
            $msg .= "_{$msgAgradecimento}_\n\n";
        }

        $msg .= "📱 Acompanhe sua carteira e parceiros em:\n";
        $msg .= "https://linecash.com.br";

        return $msg;
    }

    /**
     * Monta a mensagem de WhatsApp para notificação de pagamento com LineCash
     */
    public static function gerarMensagemPagamento(
        string $clienteNome,
        string $lojaNome,
        float $valorLc,
        float $saldoRestanteLc
    ): string {
        $saudacao = !empty($clienteNome) ? "Olá, *{$clienteNome}*!" : "Olá!";
        $lcFormatado = number_format($valorLc, 2, ',', '.');
        $saldoFormatado = number_format($saldoRestanteLc, 2, ',', '.');

        $msg = "✅ {$saudacao}\n\n";
        $msg .= "Você utilizou *{$lcFormatado} LC* para pagar suas compras na loja *{$lojaNome}*.\n\n";
        $msg .= "💰 *Saldo Restante na Carteira:* {$saldoFormatado} LC (R$ {$saldoFormatado})\n\n";
        $msg .= "📱 Consulte seu extrato completo em: https://linecash.com.br";

        return $msg;
    }

    /**
     * Gera o link direto wa.me para abertura de chat com mensagem pré-preenchida
     */
    public static function gerarLinkDireto(string $whatsapp, string $mensagem): string
    {
        $numero = self::formatarNumeroWhatsapp($whatsapp);
        if (empty($numero)) {
            return '';
        }
        return 'https://api.whatsapp.com/send?phone=' . $numero . '&text=' . rawurlencode($mensagem);
    }
}
