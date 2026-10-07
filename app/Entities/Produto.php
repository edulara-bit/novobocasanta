<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Produto extends Entity
{
    protected $dates = ['pro_validade', 'created_at', 'updated_at', 'deleted_at', 'linefast_sync_at'];
    protected $casts = [
        'pro_id'            => 'integer',
        'pro_parceiro'      => 'integer',
        'pro_categoria'     => 'integer',
        'pro_categoria_pai' => 'integer',
        'pro_cidade'        => 'integer',
        'pro_preco'         => 'float',
        'pro_precodesconto' => 'float',
        'pro_apartirde'     => 'float',
        'pro_destaque'      => 'integer',
        'pro_melhor_preco'  => 'integer',
        'pro_status'        => 'integer',
        'pro_visitas'       => 'integer',
        'pro_ordem'         => 'integer',
        'linefast_stock'    => 'integer',
    ];

    /**
     * Verifica se o produto tem preço válido divulgado
     */
    public function hasPreco(): bool
    {
        $precoVenda = $this->getPrecoVenda();
        return $precoVenda > 0.00;
    }

    /**
     * Verifica se deve exibir o preço "De: R$ XX,XX"
     */
    public function hasPrecoDe(): bool
    {
        $precoNormal = (float) ($this->attributes['pro_preco'] ?? 0);
        $precoDesconto = (float) ($this->attributes['pro_precodesconto'] ?? 0);

        return ($precoNormal > 0 && $precoDesconto > 0 && $precoNormal > $precoDesconto);
    }

    /**
     * Retorna o preço original formatado em Real (R$)
     */
    public function getPrecoOriginalFormatado(): ?string
    {
        if (!$this->hasPrecoDe()) {
            return null;
        }

        return 'R$ ' . number_format((float) ($this->attributes['pro_preco'] ?? 0), 2, ',', '.');
    }

    /**
     * Retorna o preço com desconto (preço de venda real)
     */
    public function getPrecoVenda(): float
    {
        $precoDesconto = (float) ($this->attributes['pro_precodesconto'] ?? 0);
        $precoNormal = (float) ($this->attributes['pro_preco'] ?? 0);
        $aPartirDe = (float) ($this->attributes['pro_apartirde'] ?? 0);

        if ($precoDesconto > 0) return $precoDesconto;
        if ($precoNormal > 0) return $precoNormal;
        if ($aPartirDe > 0) return $aPartirDe;

        return 0.00;
    }

    /**
     * Retorna o preço de venda formatado em Real (R$) ou 'Consulte o preço'
     */
    public function getPrecoVendaFormatado(): string
    {
        $preco = $this->getPrecoVenda();
        if ($preco <= 0.00) {
            return 'Consulte o preço';
        }

        return 'R$ ' . number_format($preco, 2, ',', '.');
    }

    /**
     * Calcula a porcentagem de desconto (%)
     */
    public function getPercentualDesconto(): int
    {
        $valorOriginal = (float) ($this->attributes['pro_preco'] ?? 0);
        $valorDesconto = (float) ($this->attributes['pro_precodesconto'] ?? 0);

        if ($valorOriginal <= 0 || $valorDesconto <= 0 || $valorDesconto >= $valorOriginal) {
            return 0;
        }

        $desconto = (($valorOriginal - $valorDesconto) / $valorOriginal) * 100;
        return (int) round($desconto);
    }

    /**
     * Verifica se o produto tem integração ativa com o Linefast
     */
    public function isLinefast(): bool
    {
        return !empty($this->attributes['linefast_product_id']) || ($this->attributes['origem'] ?? '') === 'linefast';
    }

    /**
     * Gera o link direto de checkout / adicionar ao carrinho do Linefast
     */
    public function getLinefastCartUrl(int $quantity = 1): string
    {
        if (!empty($this->attributes['linefast_buy_url'])) {
            return $this->attributes['linefast_buy_url'];
        }

        $config = config('Linefast');
        $baseUrl = $config->cartUrl ?? 'https://linefast.com.br/cart/add';
        $lfId = $this->attributes['linefast_product_id'] ?? $this->attributes['pro_id'];

        return "{$baseUrl}?product_id={$lfId}&qty={$quantity}&ref=bocasanta";
    }

    /**
     * Retorna o link de consulta no WhatsApp
     */
    public function getWhatsappConsultaLink(string $whatsAppNumero = ''): string
    {
        $whats = preg_replace('/\D/', '', $whatsAppNumero ?: ($this->attributes['par_whatsapp'] ?? $this->attributes['par_telefone'] ?? ''));
        if (strlen($whats) === 10 || strlen($whats) === 11) {
            $whats = '55' . $whats;
        }

        $titulo = $this->attributes['pro_titulo'] ?? 'Oferta';
        $msg = urlencode("Olá! Vi o anúncio \"{$titulo}\" no Boca Santa Ofertas e gostaria de consultar o preço e mais informações.");
        return "https://api.whatsapp.com/send?phone={$whats}&text={$msg}";
    }

    /**
     * Retorna a URL da imagem principal da oferta com fallback online em desenvolvimento
     */
    public function getImagemUrl(): string
    {
        $foto = $this->attributes['fot_imagem'] ?? $this->attributes['pro_foto'] ?? $this->attributes['fot_thumb'] ?? $this->attributes['pro_thumb'] ?? '';
        $parceiroId = $this->attributes['fot_parceiro'] ?? $this->attributes['pro_parceiro'] ?? '';

        if (!empty($foto)) {
            if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
                return $foto;
            }

            // 1. Verifica se existe localmente
            if (!empty($parceiroId) && file_exists(FCPATH . 'upimg/produtos/' . $parceiroId . '/' . $foto)) {
                return base_url('upimg/produtos/' . $parceiroId . '/' . $foto);
            }
            if (file_exists(FCPATH . 'upimg/produtos/' . $foto)) {
                return base_url('upimg/produtos/' . $foto);
            }

            // 2. Fallback online em desenvolvimento
            if (!empty($parceiroId)) {
                return "https://www.bocasantaofertas.com.br/upimg/produtos/{$parceiroId}/{$foto}";
            }
            return "https://www.bocasantaofertas.com.br/upimg/produtos/{$foto}";
        }

        return base_url('assets/images/sem_foto.gif');
    }

    /**
     * Gera a URL canônica amigável para SEO
     */
    public function getUrl(string $cidadeSlug = '', string $categoriaSlug = ''): string
    {
        $cidade = !empty($cidadeSlug) ? $cidadeSlug : ($this->attributes['cid_url'] ?? 'piracicaba');
        $categoria = !empty($categoriaSlug) ? $categoriaSlug : ($this->attributes['cat_url'] ?? 'ofertas');
        $slug = $this->attributes['pro_slug'] ?? 'oferta-' . $this->attributes['pro_id'];

        return base_url("{$cidade}/{$categoria}/{$slug}");
    }
}
