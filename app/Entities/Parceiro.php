<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Parceiro extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'linefast_sync_at'];
    protected $casts = [
        'par_id'         => 'integer',
        'par_cidade'     => 'integer',
        'par_estado'     => 'integer',
        'par_status'     => 'integer',
        'linefast_ativo' => 'boolean',
    ];

    /**
     * Retorna o nome exibível do parceiro
     */
    public function getNome(): string
    {
        return $this->attributes['par_nome'] ?? $this->attributes['par_fantasia'] ?? 'Parceiro';
    }

    /**
     * Retorna o identificador slug amigável do parceiro
     */
    public function getSlug(): string
    {
        if (!empty($this->attributes['par_apelido'])) {
            return (string)$this->attributes['par_apelido'];
        }
        return 'parceiro-' . $this->attributes['par_id'];
    }

    /**
     * Retorna a logo do parceiro ou avatar padrão com fallback online
     */
    public function getLogoUrl(): string
    {
        $logo = $this->attributes['par_imagem'] ?? '';
        if (!empty($logo)) {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }
            if (file_exists(FCPATH . 'upimg/parceiros/' . $logo)) {
                return base_url('upimg/parceiros/' . $logo);
            }
            if (file_exists(FCPATH . 'upimg/' . $logo)) {
                return base_url('upimg/' . $logo);
            }

            // Fallback online em desenvolvimento
            return "https://www.bocasantaofertas.com.br/upimg/{$logo}";
        }

        return base_url('assets/images/logo-small.png');
    }

    /**
     * Retorna o link formatado para WhatsApp
     */
    public function getWhatsappLink(): ?string
    {
        $whats = preg_replace('/\D/', '', $this->attributes['par_whatsapp'] ?? $this->attributes['par_telefone'] ?? '');
        if (empty($whats)) {
            return null;
        }

        if (strlen($whats) === 10 || strlen($whats) === 11) {
            $whats = '55' . $whats;
        }

        $mensagem = urlencode("Olá! Vi o anúncio no Boca Santa Ofertas e gostaria de mais informações.");
        return "https://api.whatsapp.com/send?phone={$whats}&text={$mensagem}";
    }

    /**
     * Retorna o endereço completo formatado
     */
    public function getEnderecoCompleto(string $cidadeNome = 'Piracicaba'): string
    {
        $parts = [];
        if (!empty($this->attributes['par_endereco'])) {
            $rua = $this->attributes['par_endereco'];
            if (!empty($this->attributes['par_numero'])) {
                $rua .= ', ' . $this->attributes['par_numero'];
            }
            if (!empty($this->attributes['par_complemento'])) {
                $rua .= ' - ' . $this->attributes['par_complemento'];
            }
            $parts[] = $rua;
        }
        if (!empty($this->attributes['par_bairro'])) {
            $parts[] = $this->attributes['par_bairro'];
        }
        $parts[] = $cidadeNome . ' - SP';
        if (!empty($this->attributes['par_cep'])) {
            $parts[] = 'CEP: ' . $this->attributes['par_cep'];
        }

        return implode(' - ', $parts);
    }

    /**
     * Retorna o HTML ou URL do Google Maps
     */
    public function getGoogleMapsEmbedUrl(string $cidadeNome = 'Piracicaba'): string
    {
        $raw = $this->attributes['par_maps'] ?? '';
        if (!empty($raw) && (str_contains($raw, 'maps.google.com') || str_contains($raw, 'google.com/maps'))) {
            if (preg_match('/src="([^"]+)"/', $raw, $m)) {
                return $m[1];
            }
            if (str_starts_with($raw, 'http')) {
                return $raw;
            }
        }

        // Gera a partir do endereço
        $endereco = $this->getEnderecoCompleto($cidadeNome);
        return "https://maps.google.com/maps?q=" . urlencode($endereco) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
    }

    /**
     * Verifica se o parceiro está conectado e ativo no Linefast
     */
    public function isLinefastAtivo(): bool
    {
        return !empty($this->attributes['linefast_partner_id']) && (bool) ($this->attributes['linefast_ativo'] ?? false);
    }

    /**
     * Retorna a URL amigável da página do parceiro
     */
    public function getUrl(string $cidadeSlug = ''): string
    {
        $cidade = !empty($cidadeSlug) ? $cidadeSlug : ($this->attributes['cid_url'] ?? 'piracicaba');
        $slug = $this->getSlug();

        return base_url("{$cidade}/parceiro/{$slug}");
    }
}
