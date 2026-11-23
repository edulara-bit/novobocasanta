<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Parceiro extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'linefast_sync_at'];
    protected $casts = [
        'par_id'           => 'integer',
        'par_cidade'       => 'integer',
        'par_estado'       => 'integer',
        'par_status'       => 'integer',
        'linefast_ativo'   => 'boolean',
        'linefast_is_test' => 'boolean',
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
            if (file_exists(FCPATH . 'assets/uploads/parceiros/' . $logo)) {
                return base_url('assets/uploads/parceiros/' . $logo);
            }

            // Fallback online em desenvolvimento
            if (str_contains($logo, '/')) {
                return "https://www.bocasantaofertas.com.br/upimg/{$logo}";
            }
            return "https://www.bocasantaofertas.com.br/upimg/parceiros/{$logo}";
        }

        return base_url('assets/images/logo-small.png');
    }

    /**
     * Extrai o número de WhatsApp válido do parceiro
     */
    public function getWhatsappNumero(): ?string
    {
        $raw = trim((string)($this->attributes['par_whatsapp'] ?? ''));
        // No banco legado, 1 = telefone 1, 2 = telefone 2, 3 = telefone 3
        if ($raw === '1') {
            $raw = (string)($this->attributes['par_telefone'] ?? '');
        } elseif ($raw === '2') {
            $raw = (string)($this->attributes['par_telefone2'] ?? '');
        } elseif ($raw === '3') {
            $raw = (string)($this->attributes['par_telefone3'] ?? '');
        }

        $digits = preg_replace('/\D/', '', $raw);

        // Se ainda não achou número válido, procura nos telefones cadastrados
        if (strlen($digits) < 8) {
            foreach (['par_telefone', 'par_telefone2', 'par_telefone3'] as $col) {
                $tel = preg_replace('/\D/', '', (string)($this->attributes[$col] ?? ''));
                if (strlen($tel) === 11 || (strlen($tel) === 9 && str_starts_with($tel, '9'))) {
                    $digits = $tel;
                    break;
                }
            }
        }

        if (strlen($digits) === 8 || strlen($digits) === 9) {
            $digits = '19' . $digits;
        }

        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return '55' . $digits;
        }

        if (strlen($digits) === 12 || strlen($digits) === 13) {
            return $digits;
        }

        return null;
    }

    /**
     * Retorna o número de WhatsApp formatado para leitura humana: (19) 99999-9999
     */
    public function getWhatsappFormatado(): ?string
    {
        $whats = $this->getWhatsappNumero();
        if (empty($whats)) {
            return null;
        }

        $local = str_starts_with($whats, '55') ? substr($whats, 2) : $whats;
        if (strlen($local) === 11) {
            return '(' . substr($local, 0, 2) . ') ' . substr($local, 2, 5) . '-' . substr($local, 7);
        }
        if (strlen($local) === 10) {
            return '(' . substr($local, 0, 2) . ') ' . substr($local, 2, 4) . '-' . substr($local, 6);
        }

        return $whats;
    }

    /**
     * Retorna o link formatado para WhatsApp com mensagem personalizada
     */
    public function getWhatsappLink(?string $customMessage = null): ?string
    {
        $whats = $this->getWhatsappNumero();
        if (empty($whats)) {
            return null;
        }

        $msgText = $customMessage ?? "Olá! Vi o anúncio no Boca Santa Ofertas e gostaria de mais informações.";
        $mensagem = urlencode($msgText);
        return "https://api.whatsapp.com/send?phone={$whats}&text={$mensagem}";
    }

    /**
     * Retorna lista de telefones cadastrados do parceiro (excluindo vazios e duplicados)
     */
    public function getTelefones(): array
    {
        $telefones = [];
        $vistos = [];

        foreach (['par_telefone', 'par_telefone2', 'par_telefone3'] as $field) {
            $raw = trim((string)($this->attributes[$field] ?? ''));
            if (empty($raw)) continue;

            $digits = preg_replace('/\D/', '', $raw);
            if (strlen($digits) < 8) continue;

            if (strlen($digits) === 8 || strlen($digits) === 9) {
                $digits = '19' . $digits;
            }

            if (isset($vistos[$digits])) continue;
            $vistos[$digits] = true;

            if (strlen($digits) === 10) {
                $formatted = '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 4) . '-' . substr($digits, 6);
            } elseif (strlen($digits) === 11) {
                $formatted = '(' . substr($digits, 0, 2) . ') ' . substr($digits, 2, 5) . '-' . substr($digits, 7);
            } else {
                $formatted = $raw;
            }

            $telefones[] = [
                'raw'     => $raw,
                'clean'   => $digits,
                'display' => $formatted,
            ];
        }

        return $telefones;
    }

    /**
     * Verifica se é parceiro de testes/homologação
     */
    public function isLinefastTest(): bool
    {
        return (bool)($this->attributes['linefast_is_test'] ?? false);
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
        $hasId = !empty($this->attributes['linefast_partner_id']);
        $isAtivo = !empty($this->attributes['linefast_ativo']) && ((string)$this->attributes['linefast_ativo'] === '1' || $this->attributes['linefast_ativo'] === true);
        return $hasId && $isAtivo;
    }

    /**
     * Retorna o limite de ofertas do parceiro com base na integração Linefast:
     * - Integrado com Linefast (Plano Super Destaque / Integração Ativa): 0 (ilimitado)
     * - Não integrado (Planos Básico e Destaque Local): limitado a 60 ofertas
     */
    public function getLimiteOfertas(): int
    {
        return $this->isLinefastAtivo() ? 0 : 60;
    }

    /**
     * Verifica se o parceiro tem ofertas ilimitadas
     */
    public function hasOfertasIlimitadas(): bool
    {
        return $this->getLimiteOfertas() === 0;
    }

    /**
     * Retorna a URL da loja oficial do parceiro no Linefast
     */
    public function getLinefastStoreUrl(string $cidadeSlug = 'piracicaba'): string
    {
        if (!$this->isLinefastAtivo()) {
            return '';
        }

        $cidade = !empty($this->attributes['cid_url']) ? $this->attributes['cid_url'] : $cidadeSlug;
        $slug = !empty($this->attributes['linefast_store_id']) ? $this->attributes['linefast_store_id'] : ($this->attributes['par_apelido'] ?? '');

        if (!empty($slug)) {
            return "https://linefast.com.br/{$cidade}/{$slug}";
        }

        return "https://linefast.com.br";
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
